<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('active'); // active эсвэл sold
            $table->string('title');            // Барааны нэр
            $table->string('category_name')->nullable(); // Ангиллын нэр    
            $table->text('description')->nullable(); // Худалдагчийн дэлгэрэнгүй тайлбар
            $table->string('price');            // Үнэ (Жишээ нь: 3,450,000 ₮)
            $table->string('condition');        // Төлөв (Маш сайн, Шинэ гэх мэт)
            $table->string('conditionColor');   // CSS класс (Таны React дээрх өнгө)
            $table->string('isUsed');           // Хэрэглэсэн эсвэл Шинэ
            $table->boolean('isVerified')->default(false); // Баталгаажсан эсэх
            $table->string('img');              // Зургийн URL
            $table->json('images')->nullable(); // Олон зураг хадгалах багана (Array хэлбэрээр)
            $table->decimal('weight', 8, 2)->nullable(); // Жин (Кг)
            $table->string('size_category')->default('medium'); // Хэмжээ: small, medium, large
            $table->json('specs')->nullable(); // Нарийвчилсан үзүүлэлтүүд (Брэнд, Он, Материал г.м)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
