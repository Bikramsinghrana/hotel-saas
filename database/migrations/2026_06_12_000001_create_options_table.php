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
        Schema::create('options', function (Blueprint $table) {
            $table->id();

            // Tenant & Hotel Scoping (Multi-Tenant SaaS architecture)
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained('hotels')->cascadeOnDelete();

            // Categorization & Key-Value Pair
            $table->string('group', 50)->default('general')->index(); // 'general', 'pagination', 'tax_gst', 'currency', 'booking', 'invoice', 'hotel', 'restaurant', 'notification'
            $table->string('key', 100)->index();
            $table->longText('value')->nullable();
            $table->string('type', 30)->default('string'); // 'string', 'number', 'boolean', 'json', 'array', 'select', 'color', 'file'

            // Admin UI & Metadata
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->json('options_list')->nullable(); // For select/radio dropdown options

            // System & Performance Flags
            $table->boolean('is_autoload')->default(true)->index(); // Cached on app boot for lightning performance
            $table->boolean('is_public')->default(false)->index();   // Accessible on frontend / JS configs
            $table->boolean('is_system')->default(false);           // Core settings protected from deletion
            $table->boolean('status')->default(true)->index();

            $table->softDeletes();
            $table->timestamps();

            // Unique index per tenant, hotel & key (handling soft deletes)
            $table->index(['tenant_id', 'hotel_id', 'key', 'status'], 'tenant_hotel_option_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};
