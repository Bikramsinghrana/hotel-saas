<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('author_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('hotel_slug')->nullable()->index();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('rating')->default(3);
            $table->enum('status', ['pending', 'draft', 'active', 'inactive'])->default('draft');
            $table->json('address')->nullable();
            $table->json('nearby')->nullable();
            $table->decimal('base_price',10,2)->default(0);
            $table->decimal('discount',8,2)->default(0);
            $table->decimal('tax',8,2)->default(0);
            $table->unsignedBigInteger('thumbnail_id')->nullable();
            $table->json('gallery')->nullable();
            $table->json('facilities')->nullable();
            $table->json('policies')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hotels');
    }
};
