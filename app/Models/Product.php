<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $with = ['user'];

    // Бааз руу хадгалахыг зөвшөөрөх баганууд
    protected $fillable = [
        'user_id',     // <--- ШИНЭЭР НЭМСЭН (Худалдагчийн ID)
        'status',      // <--- ШИНЭЭР НЭМСЭН (Идэвхтэй эсвэл зарагдсан төлөв)
        'title',
        'category_name',
        'description',
        'price',
        'condition',
        'conditionColor',
        'isUsed',
        'isVerified',
        'img',
        'images',
        'weight',
        'size_category',
        'specs'
    ];

    // JSON мэдээллийг автоматаар Array (Жагсаалт) болгож хөрвүүлэх тохиргоо
    protected $casts = [
        'images' => 'array',
        'specs' => 'array',
    ];

    // === ШИНЭЭР НЭМСЭН: Бараа болон Худалдагчийг холбох ===
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // === ШИНЭЭР НЭМСЭН ХЭСЭГ: Бараа зарагдах болон устгагдах үед автоматаар ажиллах цэвэрлэгээ ===
    protected static function booted()
    {
        // 1. Барааны мэдээлэл ШИНЭЧЛЭГДЭХ (Update) үед ажиллана
        static::updated(function ($product) {
            // Хэрэв барааны төлөв 'sold' (зарагдсан) болж өөрчлөгдсөн бол:
            if ($product->isDirty('status') && $product->status === 'sold') {
                // Бүх хүмүүсийн сагснаас устгах
                \App\Models\CartItem::where('product_id', $product->id)->delete();

                // Бүх хүмүүсийн хадгалсан жагсаалтаас устгах
                \App\Models\Favorite::where('product_id', $product->id)->delete();
            }
        });

        // 2. Барааг БҮР МӨСӨН УСТГАХ (Delete) үед бас давхар цэвэрлэнэ
        static::deleting(function ($product) {
            \App\Models\CartItem::where('product_id', $product->id)->delete();
            \App\Models\Favorite::where('product_id', $product->id)->delete();
        });
    }
}
