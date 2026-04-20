<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('verification_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Баталгаажуулах бараа
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade'); // Хүсэлт явуулсан худалдан авагч
            $table->string('status')->default('pending'); // pending (Хүлээгдэж буй), approved (Зөвшөөрсөн), rejected (Татгалзсан)
            $table->text('admin_note')->nullable(); // Админ татгалзсан шалтгаанаа бичих
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_requests');
    }
};
