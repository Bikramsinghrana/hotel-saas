<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('code')->nullable()->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('discount_value', 10, 2);
            $table->date('start_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->string('validity_type')->default('all')->comment('all, date, days, time, days_time, custom');
            
            // Dynamic Days & Time Scheduling
            $table->json('applicable_days')->nullable()->comment('Array of days: sunday, monday, etc.');
            $table->time('start_time')->nullable()->comment('e.g. 12:30:00');
            $table->time('end_time')->nullable()->comment('e.g. 15:30:00');
            $table->string('time_slot')->nullable()->comment('e.g. 12:30 - 15:30, lunch, happy_hours, custom');

            $table->decimal('min_spend', 10, 2)->default(0);
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);

            $table->string('type')->default('coupon')->comment('coupon, offer');
            $table->boolean('status')->default(true);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
