<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        // ШИНЭЧИЛСЭН ХЭСЭГ: Token-оос хэрэглэгчийн ID-г шууд авна
        $userId = Auth::id();

        $favorite = Favorite::where('user_id', $userId)
            ->where('product_id', $request->product_id)
            ->first();

        if ($favorite) {
            $favorite->delete(); // Аль хэдийн лайк дарсан бол устгана (Unlike)
            return response()->json(['status' => 'removed']);
        } else {
            Favorite::create([
                'user_id' => $userId,
                'product_id' => $request->product_id
            ]); // Байхгүй бол нэмнэ (Like)
            return response()->json(['status' => 'added']);
        }
    }
}
