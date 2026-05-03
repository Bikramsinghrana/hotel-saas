<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('hotel_id')->constrained('hotels');
            $table->foreignId('room_id')->constrained('rooms');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('days');
            $table->integer('quantity')->default(1);
            $table->decimal('base_price',10,2);
            $table->decimal('discount',8,2)->default(0);
            $table->decimal('tax',8,2)->default(0);
            $table->decimal('total_price',10,2);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
