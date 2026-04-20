<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Худалдан авагч
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Ямар бараа болох
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade'); // Худалдагч хэн болох

            $table->string('phone')->nullable(); // Хүргэлтийн утас
            $table->text('address')->nullable(); // Хүргэлтийн хаяг

            $table->decimal('total_price', 15, 2);
            $table->string('status')->default('pending'); // pending, paid, shipped, delivered, cancelled

            // QPay болон Төлбөрийн мэдээлэл хадгалах хэсэг
            $table->string('payment_method')->default('qpay');
            $table->string('qpay_invoice_id')->nullable(); // QPay-ээс ирэх нэхэмжлэхийн дугаар

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
