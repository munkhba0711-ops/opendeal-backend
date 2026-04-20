<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // 1. N+1 гацалтыг засаж user-ийг давхар дуудлаа
        $query = Product::with('user')->where('status', 'active');

        // 1. Төрлөөр шүүх
        if ($request->filled('category')) {
            $query->where('category_name', $request->category);
        }

        // Хайлтаар шүүх
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('category_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // 2. ТӨЛӨВӨӨР ШҮҮХ
        if ($request->filled('conditions')) {
            $conds = explode(',', $request->conditions);
            $query->whereIn('condition', $conds);
        }

        // 3. Үнээр шүүх (PostgreSQL дээр гацахгүй ажиллахаар засав)
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereRaw("CAST(REPLACE(REPLACE(price, ' ₮', ''), ',', '') AS INTEGER) BETWEEN ? AND ?", [
                $request->min_price,
                $request->max_price
            ]);
        }

        // 4. Эрэмбэлэх (PostgreSQL дээр гацахгүй ажиллахаар засав)
        if ($request->sort === 'low') {
            $query->orderByRaw("CAST(REPLACE(REPLACE(price, ' ₮', ''), ',', '') AS INTEGER) ASC");
        } elseif ($request->sort === 'high') {
            $query->orderByRaw("CAST(REPLACE(REPLACE(price, ' ₮', ''), ',', '') AS INTEGER) DESC");
        } else {
            $query->latest();
        }

        return response()->json($query->paginate(9));
    }

    // 1. Нэг барааны мэдээллийг ID-гаар нь татаж авах
    public function show($id)
    {
        // === ЭНД with('user') гэж заавал нэмэх ёстой (Чат руу нэр зураг нь очно) ===
        $product = Product::with('user')->find($id);

        if (!$product) {
            return response()->json(['message' => 'Бараа олдсонгүй'], 404);
        }

        $relatedProducts = Product::where('category_name', $product->category_name)
            ->where('id', '!=', $id)
            ->latest()
            ->take(4)
            ->get();

        return response()->json([
            'product' => $product,
            'related' => $relatedProducts
        ]);
    }

    // 2. Үнийн санал (Purchase Request) илгээх
    public function submitRequest(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'offered_price' => 'required|numeric|min:0',
        ]);

        \App\Models\PurchaseRequest::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'product_id' => $request->product_id,
            'offered_price' => $request->offered_price,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Үнийн санал амжилттай илгээгдлээ!'
        ]);
    }
    // === ШИНЭЧИЛСЭН: Барааг шинэчлэх (Засах) ===
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Зөвхөн барааны эзэн л засах эрхтэй
        if ($product->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            return response()->json(['message' => 'Танд энэ барааг засах эрх байхгүй байна!'], 403);
        }

        // 1. Хуучин үлдсэн зурагнуудыг авах (Устгаагүй үлдээсэн зурагнууд)
        $existingImages = $request->has('existing_images') ? json_decode($request->existing_images, true) : [];
        if (!is_array($existingImages)) {
            $existingImages = [];
        }

        // 2. Шинээр нэмсэн зурагнуудыг хадгалах
        $newImageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $newImageUrls[] = asset('storage/' . $path);
            }
        }

        // Хуучин болон Шинэ зурагнуудыг нэгтгэх
        $allImages = array_merge($existingImages, $newImageUrls);
        $mainImageUrl = count($allImages) > 0 ? $allImages[0] : $product->img;

        // 3. Өнгө болон Үнэ тодорхойлох
        $conditionColors = [
            'Шинэ' => 'bg-blue-100 text-blue-700',
            'Маш сайн' => 'bg-emerald-100 text-emerald-700',
            'Сайн' => 'bg-primary/10 text-primary',
            'Дунд зэрэг' => 'bg-orange-100 text-orange-700',
            'Хуучин' => 'bg-red-100 text-red-700',
        ];
        $cond = $request->condition ?? 'Сайн';
        $color = $conditionColors[$cond] ?? 'bg-slate-100 text-slate-700';

        // Үнийг цэвэрлэж байгаад дахин форматлах
        $cleanPrice = preg_replace('/[^0-9]/', '', $request->price);
        $formattedPrice = number_format((int)$cleanPrice) . ' ₮';

        // 4. Баазад шинэчлэх
        $product->update([
            'title' => $request->title,
            'category_name' => $request->category_name,
            'description' => $request->description,
            'price' => $formattedPrice,
            'condition' => $cond,
            'conditionColor' => $color,
            'isUsed' => $request->isUsed ?? 'Хэрэглэсэн',
            'weight' => $request->weight,
            'size_category' => $request->size_category ?? 'medium',
            'img' => $mainImageUrl,
            'images' => $allImages,
            'specs' => $request->specs ? json_decode($request->specs, true) : $product->specs,
        ]);

        return response()->json([
            'message' => 'Бараа амжилттай шинэчлэгдлээ!',
            'product' => $product
        ]);
    }

    // Барааг устгах
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Зөвхөн барааны эзэн л устгах эрхтэй
        if ($product->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            return response()->json(['message' => 'Танд энэ барааг устгах эрх байхгүй байна!'], 403);
        }

        $product->delete();

        return response()->json(['message' => 'Бараа амжилттай устгагдлаа!']);
    }
    // === ШИНЭЭР НЭМЭХ: ШИНЭ БАРАА ОРУУЛАХ ФУНКЦ ===
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_name' => 'required|string',
            'price' => 'required|numeric',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120' // 5MB хүртэл
        ]);

        // 1. Зурагнуудыг хадгалах
        $imageUrls = [];
        $mainImageUrl = null;

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('products', 'public');
                $url = asset('storage/' . $path);
                $imageUrls[] = $url;

                // Эхний зургийг нүүр зураг болгох
                if ($index === 0) {
                    $mainImageUrl = $url;
                }
            }
        }

        // 2. Condition color тодорхойлох
        $conditionColors = [
            'Шинэ' => 'bg-blue-100 text-blue-700',
            'Маш сайн' => 'bg-emerald-100 text-emerald-700',
            'Сайн' => 'bg-primary/10 text-primary',
            'Дунд зэрэг' => 'bg-orange-100 text-orange-700',
            'Хуучин' => 'bg-red-100 text-red-700',
        ];
        $cond = $request->condition ?? 'Сайн';
        $color = $conditionColors[$cond] ?? 'bg-slate-100 text-slate-700';

        // 3. Үнэ форматлах (Жишээ нь: 1500000 -> 1,500,000 ₮)
        $formattedPrice = number_format($request->price) . ' ₮';

        // 4. Баазад хадгалах
        $product = Product::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'status' => 'active',
            'title' => $request->title,
            'category_name' => $request->category_name,
            'description' => $request->description,
            'price' => $formattedPrice,
            'condition' => $cond,
            'conditionColor' => $color,
            'isUsed' => $request->isUsed ?? 'Хэрэглэсэн',
            'isVerified' => 0, // Анх оруулахад баталгаажаагүй байна
            'weight' => $request->weight,
            'size_category' => $request->size_category ?? 'medium',
            'img' => $mainImageUrl ?? 'https://via.placeholder.com/800x600?text=Зураг+Олдсонгүй',
            'images' => $imageUrls,
            'specs' => $request->specs ? json_decode($request->specs, true) : null, // React-аас ирсэн JSON-ийг хөрвүүлж хадгалах
        ]);

        return response()->json([
            'message' => 'Бараа амжилттай нийтлэгдлээ!',
            'product' => $product
        ], 201);
    }

    // === ШИНЭ: ХАЙЛТАНД ҮГ САНАЛ БОЛГОХ ===
    public function searchSuggestions(Request $request)
    {
        $term = $request->query('q');
        if (!$term) {
            return response()->json([]);
        }

        $suggestions = Product::where('status', 'active')
            ->where('title', 'LIKE', "%{$term}%")
            ->select('id', 'title', 'category_name')
            ->latest()
            ->take(5)
            ->get();

        return response()->json($suggestions);
    }
}
