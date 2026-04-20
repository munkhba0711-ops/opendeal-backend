<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    // Баазад хадгалахыг зөвшөөрөх баганууд
    protected $fillable = ['user_id', 'product_id'];

    // ШИНЭЭР НЭМСЭН ХЭСЭГ: Барааны хүснэгттэй холбох холбоос
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
