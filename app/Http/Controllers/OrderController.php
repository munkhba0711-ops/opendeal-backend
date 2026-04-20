<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // === ШИНЭ: ЗАХИАЛГА ӨГӨГДЛИЙН САНД ХАДГАЛАХ ===
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'shipping_info' => 'required|array',
            'total_price' => 'required|numeric'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Захиалга үүсгэх
        $order = new Order();
        $order->user_id = Auth::id(); // Худалдан авагч
        $order->product_id = $product->id;
        $order->seller_id = $product->user_id; // Худалдагч эзэн нь

        // Хүргэлтийн мэдээллийг нэгтгэх
        $info = $request->shipping_info;
        $order->phone = $info['phone'] ?? null;
        $order->address = ($info['city'] ?? '') . ', ' . ($info['district'] ?? '') . ', ' . ($info['address'] ?? '');

        $order->total_price = $request->total_price;
        $order->status = 'pending'; // Хүлээгдэж буй (Төлбөрөө төлөөгүй)
        $order->save();

        return response()->json([
            'message' => 'Захиалга амжилттай үүслээ',
            'order' => $order
        ]);
    }

    // Төлбөр амжилттай болсныг батлах функц (Өмнө нь хийсэн)
    public function markAsPaid($id)
    {
        $order = Order::findOrFail($id);

        $order->status = 'paid';
        $order->save();

        $product = Product::find($order->product_id);
        if ($product) {
            $product->status = 'sold';
            $product->save();

            // === ШИНЭЭР НЭМСЭН: Бусад бүх хүмүүсийн сагс, хадгалснаас устгах ===
            \App\Models\CartItem::where('product_id', $product->id)->delete();
            \App\Models\Favorite::where('product_id', $product->id)->delete();
        }

        return response()->json(['message' => 'Төлбөр амжилттай баталгаажлаа', 'order' => $order]);
    }
}
