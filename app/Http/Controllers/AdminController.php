<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    private function isAdmin()
    {
        return Auth::user() && Auth::user()->role === 'admin';
    }

    // --- 1. СТАТИСТИК БОЛОН ТАЙЛАН (Dashboard) ---
    public function getDashboardStats()
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $today = \Carbon\Carbon::today();

        $stats = [
            'totalUsers' => User::count(),
            'newUsersToday' => User::whereDate('created_at', $today)->count(),
            'totalProducts' => Product::count(),
            'totalTransactions' => Order::whereIn('status', ['paid', 'shipping', 'delivered'])->sum('total_price'),
            'transactionsToday' => Order::whereDate('created_at', $today)->whereIn('status', ['paid', 'shipping', 'delivered'])->sum('total_price'),
            'pendingVerifications' => DB::table('verification_requests')->where('status', 'pending')->count(),
            'pendingReports' => DB::table('reports')->where('status', 'pending')->count(),
        ];

        return response()->json($stats);
    }

    // --- 2. БАРААНЫ ХЯНАЛТ ---
    public function getProducts(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $type = $request->query('type', 'all'); // all, pending_verification

        if ($type === 'pending_verification') {
            $requests = DB::table('verification_requests')
                ->join('products', 'verification_requests.product_id', '=', 'products.id')
                ->join('users', 'verification_requests.buyer_id', '=', 'users.id')
                ->where('verification_requests.status', 'pending')
                ->select('verification_requests.id as request_id', 'verification_requests.created_at as req_date', 'products.*', 'users.name as seller_name')
                ->get();
            return response()->json($requests);
        }

        $products = Product::with('user')->latest()->get();
        return response()->json($products);
    }

    public function approveProduct($id) // id = verification_request.id
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $req = DB::table('verification_requests')->where('id', $id)->first();
        if ($req) {
            DB::table('verification_requests')->where('id', $id)->update(['status' => 'approved']);
            Product::where('id', $req->product_id)->update(['isVerified' => 1]);
        }
        return response()->json(['message' => 'Барааг баталгаажууллаа!']);
    }

    public function deleteProduct($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        Product::where('id', $id)->delete();
        return response()->json(['message' => 'Барааг устгалаа. Хэрэглэгч дүрмээ зөрчсөн байх магадлалтай.']);
    }

    // --- 3. ХЭРЭГЛЭГЧИЙН УДИРДЛАГА ---
    public function getUsers()
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = User::latest()->get();
        return response()->json($users);
    }

    public function toggleBlockUser($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Админ хэрэглэгчийг блоклох боломжгүй!'], 400);
        }

        $user->is_blocked = !$user->is_blocked;
        $user->save();

        $status = $user->is_blocked ? 'Блоклогдлоо' : 'Блокоос гарлаа';
        return response()->json(['message' => "Хэрэглэгч амжилттай $status."]);
    }

    // --- 4. ГОМДОЛ ШИЙДВЭРЛЭХ ---
    public function getReports()
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reports = DB::table('reports')
            ->join('users as reporter', 'reports.reporter_id', '=', 'reporter.id')
            ->join('users as reported', 'reports.reported_user_id', '=', 'reported.id')
            ->leftJoin('products', 'reports.product_id', '=', 'products.id')
            ->select(
                'reports.*',
                'reporter.name as reporter_name',
                'reported.name as reported_name',
                'products.title as product_title'
            )
            ->latest('reports.created_at')
            ->get();

        return response()->json($reports);
    }

    public function resolveReport($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::table('reports')->where('id', $id)->update(['status' => 'resolved']);
        return response()->json(['message' => 'Гомдлыг амжилттай шийдвэрлэлээ.']);
    }

    // --- 5. СИСТЕМИЙН ТОХИРГОО (Ангилал удирдах) ---
    public function getCategories()
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json(DB::table('categories')->latest()->get());
    }

    public function addCategory(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $request->validate(['name' => 'required|string|max:255']);

        DB::table('categories')->insert([
            'name' => $request->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Шинэ ангилал нэмэгдлээ.']);
    }

    public function deleteCategory($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        DB::table('categories')->where('id', $id)->delete();
        return response()->json(['message' => 'Ангилал устгагдлаа.']);
    }
}
