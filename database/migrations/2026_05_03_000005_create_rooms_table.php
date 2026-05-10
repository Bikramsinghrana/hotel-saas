<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {

            $table->id();
            
            $table->foreignId('tenant_id')->nullable()->constrained('tenants');
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('room_type_id')->nullable()->constrained('room_types')->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->cascadeOnDelete();

            // $table->string('room_type')->nullable();
            $table->string('room_slug')->nullable()->index();
            $table->string('post_title')->nullable();
            $table->longText('post_content')->nullable();
            $table->unsignedBigInteger('thumbnail_id')->nullable();
            $table->json('gallery')->nullable();
            $table->json('facilities')->nullable();
            $table->json('extra_services')->nullable();

            $table->integer('total_rooms')->default(1);
            $table->integer('number_of_bed')->default(1);
            $table->integer('max_adults')->default(2);
            $table->integer('max_children')->default(0);

            $table->double('room_footage', 10, 2)->nullable();

            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('member_price', 10, 2)->default(0);
            $table->decimal('price_per_day', 10, 2)->default(0);
            $table->decimal('discount', 8, 2)->default(0);
            $table->string('coupon')->nullable();
            $table->decimal('tax', 8, 2)->default(0);

            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            $table->integer('day')->nullable();

            $table->boolean('is_base')->default(false);
            $table->boolean('accept_terms')->default(false);

            $table->enum('status', ['pending', 'draft', 'active', 'inactive'])->default('draft')->index();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
