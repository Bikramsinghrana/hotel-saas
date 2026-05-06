<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hotel_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('type'); // amenity, guest_service, room_facility
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('icon_or_image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hotel_masters');
    }
};
