<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    // 1. Бааз руу хадгалахыг зөвшөөрөх баганууд
    protected $fillable = [
        'user_id',
        'product_id',
        'offered_price',
        'status',
    ];

    // 2. Энэ хүсэлт нь ямар Хэрэглэгчийнх болохыг заах холболт
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 3. Энэ хүсэлт нь ямар Бараан дээр хийгдсэнийг заах холболт (Профайл дээр харуулахад маш чухал)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
