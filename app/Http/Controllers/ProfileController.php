<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function getProfileData()
    {
        $userId = Auth::id();
        $user = User::find($userId);

        $activeOrders = Order::where('user_id', $userId)
            ->where('status', 'pending')
            ->with('product')
            ->latest()
            ->get();

        $pastPurchases = Order::where('user_id', $userId)
            ->whereIn('status', ['paid', 'shipping', 'delivered'])
            ->with('product')
            ->latest()
            ->get();

        $requests = PurchaseRequest::where('user_id', $userId)
            ->with('product')
            ->latest()
            ->get();

        $data = [
            'activeOrders' => $activeOrders,
            'pastPurchases' => $pastPurchases,
            'purchaseRequests' => $requests,
        ];

        if ($user && $user->role === 'seller') {
            $data['activeProducts'] = \App\Models\Product::where('user_id', $userId)->where('status', 'active')->latest()->get();
            $data['soldProducts'] = \App\Models\Product::where('user_id', $userId)->where('status', 'sold')->latest()->get();
            $data['reviews'] = \App\Models\Review::with('buyer')->where('seller_id', $userId)->latest()->get();

            $data['incomingRequests'] = PurchaseRequest::with(['product', 'user'])
                ->whereHas('product', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->latest()->get();
        }

        return response()->json($data);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json(['message' => 'Хэрэглэгч олдсонгүй'], 401);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                $oldPath = str_replace(asset('storage/'), '', $user->avatar);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = asset('storage/' . $path);
            $user->save();

            return response()->json([
                'message' => 'Зураг амжилттай шинэчлэгдлээ!',
                'user' => $user
            ]);
        }

        return response()->json(['message' => 'Файл олдсонгүй'], 400);
    }

    public function deleteAvatar(Request $request)
    {
        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json(['message' => 'Хэрэглэгч олдсонгүй'], 401);
        }

        if ($user->avatar) {
            $oldPath = str_replace(asset('storage/'), '', $user->avatar);
            Storage::disk('public')->delete($oldPath);

            $user->avatar = null;
            $user->save();

            return response()->json([
                'message' => 'Зураг амжилттай устгагдлаа.',
                'user' => $user
            ]);
        }

        return response()->json(['message' => 'Устгах зураг олдсонгүй.'], 400);
    }

    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json(['message' => 'Хэрэглэгч танигдсангүй.'], 401);
        }

        try {
            $user->name = $request->name ?? $user->name;
            $user->last_name = $request->last_name ?? $user->last_name;
            $user->first_name = $request->first_name ?? $user->first_name;
            $user->phone = $request->phone ?? $user->phone;
            $user->city = $request->city ?? $user->city;
            $user->district = $request->district ?? $user->district;
            $user->address = $request->address ?? $user->address;

            $user->save();

            return response()->json([
                'message' => 'Амжилттай хадгаллаа',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Алдаа гарлаа',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = User::find(Auth::id());

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Одоогийн нууц үг буруу байна.'], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Нууц үг амжилттай солигдлоо.']);
    }

    public function sendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Таны имэйл аль хэдийн баталгаажсан байна.'], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Баталгаажуулах линк таны имэйл рүү илгээгдлээ!']);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'hash' => 'required|string',
        ]);

        $user = User::find($request->id);

        if (!$user) {
            return response()->json(['message' => 'Хэрэглэгч олдсонгүй.'], 404);
        }

        if (sha1($user->email) !== $request->hash) {
            return response()->json(['message' => 'Баталгаажуулах код буруу байна.'], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Таны имэйл аль хэдийн баталгаажсан байна.',
                'user' => $user
            ]);
        }

        $user->markEmailAsVerified();

        return response()->json([
            'message' => 'Имэйл амжилттай баталгаажлаа!',
            'user' => $user
        ]);
    }

    public function getNotifications()
    {
        $userId = Auth::id();
        $notifications = [];

        $sentRequests = PurchaseRequest::with('product.user')->where('user_id', $userId)->latest()->take(10)->get();
        foreach ($sentRequests as $req) {
            $sellerName = $req->product->user->name ?? 'Худалдагч';
            $notifications[] = [
                'id' => 'sent_' . $req->id,
                'type' => 'sent',
                'message' => "Та $sellerName руу {$req->offered_price}₮-ийн санал явууллаа.",
                'date' => $req->created_at
            ];
        }

        $receivedRequests = PurchaseRequest::with(['product', 'user'])
            ->whereHas('product', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->latest()->take(10)->get();

        foreach ($receivedRequests as $req) {
            $buyerName = $req->user->name ?? 'Худалдан авагч';
            $notifications[] = [
                'id' => 'received_' . $req->id,
                'type' => 'received',
                'message' => "$buyerName таны '{$req->product->title}' бараанд {$req->offered_price}₮-ийн санал илгээлээ.",
                'date' => $req->created_at
            ];
        }

        $verifyRequests = DB::table('verification_requests')
            ->join('products', 'verification_requests.product_id', '=', 'products.id')
            ->where('verification_requests.buyer_id', $userId)
            ->select('verification_requests.*', 'products.title as product_title')
            ->orderBy('verification_requests.created_at', 'desc')
            ->take(5)->get();

        foreach ($verifyRequests as $req) {
            $notifications[] = [
                'id' => 'verify_' . $req->id,
                'type' => 'verify',
                'message' => "Та '{$req->product_title}' барааг админаар шалгуулах хүсэлт илгээлээ.",
                'date' => $req->created_at
            ];
        }

        usort($notifications, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return response()->json($notifications);
    }

    public function getSellerProfile($id)
    {
        $seller = User::findOrFail($id);

        $activeProducts = \App\Models\Product::where('user_id', $id)->where('status', 'active')->latest()->get();
        $soldProducts = \App\Models\Product::where('user_id', $id)->where('status', 'sold')->latest()->get();
        $reviews = \App\Models\Review::with('buyer')->where('seller_id', $id)->latest()->get();

        return response()->json([
            'seller' => $seller,
            'activeProducts' => $activeProducts,
            'soldProducts' => $soldProducts,
            'reviews' => $reviews
        ]);
    }

    public function submitReview(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500'
        ]);

        $buyerId = Auth::id();

        if ($buyerId == $request->seller_id) {
            return response()->json(['message' => 'Та өөртөө үнэлгээ өгөх боломжгүй!'], 400);
        }

        $existingReview = \App\Models\Review::where('seller_id', $request->seller_id)
            ->where('buyer_id', $buyerId)
            ->first();

        if ($existingReview) {
            return response()->json(['message' => 'Та энэ худалдагчид аль хэдийн үнэлгээ өгсөн байна.'], 400);
        }

        $review = \App\Models\Review::create([
            'seller_id' => $request->seller_id,
            'buyer_id' => $buyerId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Үнэлгээ амжилттай нийтлэгдлээ!', 'review' => $review]);
    }

    public function submitVerificationRequest(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $userId = Auth::id();

        $exists = DB::table('verification_requests')
            ->where('product_id', $request->product_id)
            ->where('buyer_id', $userId)
            ->first();

        if ($exists) {
            return response()->json(['message' => 'Та энэ барааг шалгуулах хүсэлт аль хэдийн илгээсэн байна.'], 400);
        }

        DB::table('verification_requests')->insert([
            'product_id' => $request->product_id,
            'buyer_id' => $userId,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Баталгаажуулах хүсэлт амжилттай илгээгдлээ. Бид шалгаад танд мэдэгдэх болно.']);
    }

    // === ҮНИЙН САНАЛ ЗӨВШӨӨРӨХ ===
    public function acceptRequest($id)
    {
        $purchaseRequest = PurchaseRequest::findOrFail($id);
        $product = Product::findOrFail($purchaseRequest->product_id);

        if ($product->user_id !== Auth::id()) {
            return response()->json(['message' => 'Эрх хүрэхгүй байна'], 403);
        }

        // ШИНЭЧИЛСЭН: Үнийн санал зөвшөөрөхөд зөвхөн цэвэр үнээр нь үүснэ (Хүргэлт ороогүй)
        Order::create([
            'user_id' => $purchaseRequest->user_id,
            'product_id' => $product->id,
            'seller_id' => $product->user_id,
            'total_price' => $purchaseRequest->offered_price,
            'status' => 'pending',
            'address' => 'Захиалга баталгаажсаны дараа бөглөнө',
            'phone' => '00000000'
        ]);

        $purchaseRequest->update(['status' => 'accepted']);

        return response()->json(['message' => 'Саналыг зөвшөөрлөө. Худалдан авагч төлбөрөө төлөх боломжтой боллоо.']);
    }

    // === БЭЛЭН ҮҮССЭН ЗАХИАЛГЫН ХАЯГ БАТАЛГААЖУУЛАХ ===
    public function updateOrderShipping(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Эрх хүрэхгүй байна'], 403);
        }

        // ШИНЭЧИЛСЭН: Checkout-ээс ирж байгаа Хүргэлт нэмэгдсэн нийт үнийг энд хадгалж авна
        $order->update([
            'address' => json_encode($request->input('shipping_info')),
            'phone' => $request->input('shipping_info')['phone'] ?? $order->phone,
            'total_price' => $request->input('total_price') ?? $order->total_price
        ]);

        return response()->json(['message' => 'Хаяг баталгаажлаа', 'order' => $order]);
    }

    public function completePayment($orderId)
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($orderId);
            $product = Product::findOrFail($order->product_id);

            $order->update(['status' => 'paid']);
            $product->update(['status' => 'sold']);

            PurchaseRequest::where('product_id', $product->id)
                ->where('user_id', '!=', $order->user_id)
                ->update(['status' => 'sold_to_other']);

            Order::where('product_id', $product->id)
                ->where('id', '!=', $order->id)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);

            DB::table('cart_items')->where('product_id', $product->id)->delete();
            DB::table('favorites')->where('product_id', $product->id)->delete();

            DB::commit();
            return response()->json(['message' => 'Төлбөр амжилттай. Бараа таных боллоо!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Алдаа гарлаа', 'error' => $e->getMessage()], 500);
        }
    }

    public function declineRequest($id)
    {
        $request = PurchaseRequest::findOrFail($id);
        $product = Product::findOrFail($request->product_id);

        if ($product->user_id !== Auth::id()) {
            return response()->json(['message' => 'Эрх хүрэхгүй байна'], 403);
        }

        $request->update(['status' => 'declined']);

        Order::where('user_id', $request->user_id)
            ->where('product_id', $product->id)
            ->where('status', 'pending')
            ->delete();

        return response()->json(['message' => 'Саналыг татгалзлаа.']);
    }

    // ХҮРГЭЛТ БОДОХ ТУСЛАХ ФУНКЦ
    private function calculateShipping($product)
    {
        $fee = 10000;
        if (!empty($product->weight)) {
            $fee += ceil($product->weight) * 1000;
        }
        if ($product->size_category === 'large') {
            $fee += 15000;
        } elseif ($product->size_category === 'medium') {
            $fee += 5000;
        }
        return $fee;
    }

    // САГСААР ЗАХИАЛГА ҮҮСГЭХ
    public function checkoutCart(Request $request)
    {
        DB::beginTransaction();
        try {
            $items = $request->input('items');
            $shippingInfo = $request->input('shipping_info');
            $userId = Auth::id();
            $orderIds = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['id']);
                $price = is_numeric($item['price']) ? (int)$item['price'] : (int)preg_replace('/[^0-9]/', '', $item['price']);

                $order = Order::create([
                    'user_id' => $userId,
                    'product_id' => $product->id,
                    'seller_id' => $product->user_id,
                    'total_price' => $price + $this->calculateShipping($product),
                    'status' => 'pending',
                    'address' => json_encode($shippingInfo),
                    'phone' => $shippingInfo['phone']
                ]);
                $orderIds[] = $order->id;
            }

            DB::commit();
            return response()->json(['message' => 'Захиалга үүслээ', 'order_ids' => $orderIds]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Алдаа гарлаа', 'error' => $e->getMessage()], 500);
        }
    }

    // САГСНЫ ЗАХИАЛГЫН ТӨЛБӨРИЙГ ТӨЛӨХ
    public function payCartOrders(Request $request)
    {
        DB::beginTransaction();
        try {
            $orderIds = $request->input('order_ids');
            $userId = Auth::id();

            foreach ($orderIds as $id) {
                $order = Order::findOrFail($id);
                if ($order->user_id !== $userId) {
                    continue;
                }

                $order->update(['status' => 'paid']);

                $product = Product::findOrFail($order->product_id);
                $product->update(['status' => 'sold']);

                PurchaseRequest::where('product_id', $product->id)->where('user_id', '!=', $userId)->update(['status' => 'sold_to_other']);
                Order::where('product_id', $product->id)->where('id', '!=', $order->id)->where('status', 'pending')->update(['status' => 'cancelled']);

                // === ЗАССАН ХЭСЭГ: Зөвхөн өөрийнхөө биш, БҮХ хүмүүсийн сагс болон хадгалснаас устгах ===
                DB::table('cart_items')->where('product_id', $product->id)->delete();
                DB::table('favorites')->where('product_id', $product->id)->delete();
            }

            DB::commit();
            return response()->json(['message' => 'Төлбөр амжилттай төлөгдлөө!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Алдаа гарлаа'], 500);
        }
    }
    // === ШИНЭ: ЗАХИАЛГА ЦУЦЛАХ / УСТГАХ ===
    public function cancelOrder($id)
    {
        $order = Order::findOrFail($id);

        // Зөвхөн өөрийнхөө захиалгыг устгах эрхтэй
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Эрх хүрэхгүй байна'], 403);
        }

        // Зөвхөн хүлээгдэж буй захиалгыг устгана
        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Зөвхөн хүлээгдэж буй захиалгыг устгах боломжтой'], 400);
        }

        // Баазаас бүр мөсөн устгаж цэвэрлэх
        $order->delete();

        return response()->json(['message' => 'Захиалга амжилттай устгагдлаа.']);
    }
    // === ИЛГЭЭСЭН ХҮСЭЛТ УСТГАХ ФУНКЦ ===
    public function cancelPurchaseRequest($id)
    {
        // 1. Хүсэлтийг баазаас хайж олох
        $request = \App\Models\PurchaseRequest::findOrFail($id);

        // 2. Зөвхөн өөрийнхөө илгээсэн хүсэлтийг устгах эрхтэй эсэхийг шалгах
        if ($request->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            return response()->json(['message' => 'Эрх хүрэхгүй байна'], 403);
        }

        // 3. Баазаас бүр мөсөн устгах
        $request->delete();

        return response()->json(['message' => 'Хүсэлт амжилттай устгагдлаа.']);
    }

    // === ШИНЭ: ГОМДОЛ ИЛГЭЭХ ===
    public function submitReport(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'reason' => 'required|string|max:1000',
        ], [
            'email.exists' => 'Ийм имэйлтэй хэрэглэгч олдсонгүй.'
        ]);

        $reportedUser = User::where('email', $request->email)->first();

        if ($reportedUser->id === Auth::id()) {
            return response()->json(['message' => 'Та өөрийгөө репортлох боломжгүй!'], 400);
        }

        DB::table('reports')->insert([
            'reporter_id' => Auth::id(),
            'reported_user_id' => $reportedUser->id,
            'product_id' => null, // Хэрэв тодорхой бараа заагаагүй бол
            'reason' => $request->reason,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Гомдол амжилттай илгээгдлээ. Админ шалгах болно.']);
    }
}
