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
        Schema::create('room_orders', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('order_number')->unique();
            $table->string('order_type')->default('room_booking');

            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->integer('total_person')->default(1);
            $table->integer('total_nights')->default(1);

            $table->string('customer_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->text('address')->nullable();

            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postcode')->nullable();

            $table->decimal('sub_total', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);

            $table->string('transaction_id')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->string('payment_method')->nullable();

            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');

            $table->timestamp('paid_at')->nullable();

            $table->longText('payment_response')->nullable(); // change_log

            $table->text('notes')->nullable();
            $table->text('special_request')->nullable();

            $table->ipAddress('ip_address')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_orders');
    }
};
