<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Favorite; // ШИНЭ: Favorite моделийг энд дуудаж оруулж ирэх хэрэгтэй
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // ШИНЭЭР НЭМСЭН: Хэрэглэгчийн сагс болон хадгалсан барааг баазаас татах
    public function getUserItems()
    {
        $userId = Auth::id();

        // Сагсан дахь бараанууд болон тэдгээрийн дэлгэрэнгүйг (product) татах
        $cart = CartItem::with('product')->where('user_id', $userId)->get();

        // Хадгалсан бараанууд болон тэдгээрийн дэлгэрэнгүйг (product) татах
        $favorites = Favorite::with('product')->where('user_id', $userId)->get();

        return response()->json([
            'cart' => $cart,
            'favorites' => $favorites
        ]);
    }

    // Сагсанд нэмэх эсвэл тоог нэмэх
    public function store(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $userId = Auth::id();

        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            CartItem::create([
                'user_id' => $userId,
                'product_id' => $request->product_id,
                'quantity' => 1
            ]);
        }

        return response()->json(['message' => 'Сагсанд амжилттай нэмэгдлээ']);
    }

    // Сагснаас устгах функц
    public function remove(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $userId = Auth::id();

        // Баазаас тухайн хэрэглэгчийн сагсан дахь тухайн барааг хайх
        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            $cartItem->delete(); // Олдвол устгана
            return response()->json(['message' => 'Сагснаас устгагдлаа']);
        }

        return response()->json(['message' => 'Бараа олдсонгүй'], 404);
    }
}
