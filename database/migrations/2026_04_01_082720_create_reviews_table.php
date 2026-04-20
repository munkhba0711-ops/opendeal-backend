<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade'); // Худалдагч
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade'); // Сэтгэгдэл бичсэн хүн
            $table->integer('rating'); // 1-5 од
            $table->text('comment'); // Сэтгэгдэл
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
