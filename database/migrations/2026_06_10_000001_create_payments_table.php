<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('room_order_id')->nullable()->constrained('room_orders')->nullOnDelete();
            $table->string('order_number')->nullable();

            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('USD');

            $table->string('payment_method')->nullable(); // e.g., card, cash
            $table->string('gateway')->nullable(); // e.g., stripe
            $table->string('transaction_id')->nullable();

            $table->enum('status', ['pending','paid','failed','refunded'])->default('pending');

            $table->longText('payment_response')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
