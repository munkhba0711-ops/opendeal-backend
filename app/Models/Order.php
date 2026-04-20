<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Бааз руу мэдээлэл бичих үед гарах алдаанаас сэргийлэх
    protected $guarded = [];

    // === БАРААТАЙ ХОЛБОХ ФУНКЦ (Алдааг засах гол хэсэг) ===
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // === ХЭРЭГЛЭГЧТЭЙ ХОЛБОХ ФУНКЦ (Давхар нэмчихвэл сайн туршлага болно) ===
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
