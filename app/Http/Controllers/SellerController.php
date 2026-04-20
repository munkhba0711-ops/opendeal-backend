<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Review;

class SellerController extends Controller
{
    public function show($id)
    {
        $seller = User::findOrFail($id);

        $activeProducts = Product::where('user_id', $id)->where('status', 'active')->latest()->get();
        $soldProducts = Product::where('user_id', $id)->where('status', 'sold')->latest()->get();

        // Сэтгэгдэл бичсэн хүний нэртэй хамт татах
        $reviews = Review::with('buyer')->where('seller_id', $id)->latest()->get();

        $avgRating = $reviews->avg('rating') ?? 0;
        $totalSales = $soldProducts->count() * 12; // Жишээ дата (Бодит борлуулалт)

        return response()->json([
            'seller' => $seller,
            'activeProducts' => $activeProducts,
            'soldProducts' => $soldProducts,
            'reviews' => $reviews,
            'stats' => [
                'avgRating' => round($avgRating, 1),
                'totalSales' => $totalSales,
                'activeCount' => $activeProducts->count()
            ]
        ]);
    }
}
