<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        // 1. Хэрэглэгчийн хүснэгтэд Block хийх эрх нэмэх
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_blocked')->default(false)->after('role');
        });

        // 2. Системийн тохиргоо: Хөгжмийн зэмсгийн ангиллууд
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        // 3. Гомдол маргааны хүснэгт
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reporter_id'); // Гомдол гаргагч
            $table->unsignedBigInteger('reported_user_id'); // Гомдолд өртсөн хүн
            $table->unsignedBigInteger('product_id')->nullable();
            $table->text('reason');
            $table->string('status')->default('pending'); // pending, resolved
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('categories');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_blocked');
        });
    }
};
