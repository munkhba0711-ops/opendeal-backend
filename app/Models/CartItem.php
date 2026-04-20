<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{

    protected $fillable = ['user_id', 'product_id', 'quantity'];

    // Энэ холбоос заавал байх ёстой шүү
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
