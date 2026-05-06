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
            $table->foreignId('room_type_id')->nullable()->constrained('room_types');
            $table->string('room_type');
            $table->integer('total_rooms')->default(1);
            $table->integer('max_adults')->default(2);
            $table->integer('max_children')->default(0);
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('price_per_day', 10, 2)->default(0);
            $table->decimal('discount', 8, 2)->default(0);
            $table->decimal('tax', 8, 2)->default(0);
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            $table->integer('day')->nullable();
            // $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete(); // or cascadeOnDelete()
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
