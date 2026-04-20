<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['seller_id', 'buyer_id', 'rating', 'comment'];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
