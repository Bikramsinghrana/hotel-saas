<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels');
            $table->string('room_type');
            $table->integer('total_rooms')->default(1);
            $table->integer('max_adults')->default(2);
            $table->integer('max_children')->default(0);
            $table->decimal('price_per_day',10,2)->default(0);
            $table->decimal('discount',8,2)->default(0);
            $table->decimal('tax',8,2)->default(0);
            $table->json('facilities')->nullable();
            $table->enum('status', ['pending', 'draft', 'active', 'inactive'])->default('draft');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
};
