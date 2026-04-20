<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Бараа хайхыг хурдасгах
        Schema::table('products', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('category_name');
            $table->index('status');
        });

        // Захиалга болон Хүсэлт хайхыг хурдасгах
        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('product_id');
            $table->index('status');
        });

        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('product_id');
        });

        // Чат хайхыг хурдасгах
        Schema::table('messages', function (Blueprint $table) {
            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index('is_read');
        });
    }

    public function down()
    {
        // Хэрэв буцаах шаардлага гарвал индексүүдийг устгана
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['category_name']);
            $table->dropIndex(['status']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['product_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['product_id']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['sender_id']);
            $table->dropIndex(['receiver_id']);
            $table->dropIndex(['is_read']);
        });
    }
};
