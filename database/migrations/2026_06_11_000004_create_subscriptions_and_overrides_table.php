<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Merchant active subscriptions
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->enum('status', ['active', 'trialing', 'past_due', 'cancelled', 'expired'])->default('active');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Super Admin custom feature overrides per merchant
        Schema::create('tenant_feature_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained('features')->cascadeOnDelete();
            $table->boolean('is_enabled')->default(true); // true = force enable, false = force revoke
            $table->string('custom_limit')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'feature_id']);
        });

        // Super Admin custom sub-theme access per merchant
        Schema::create('tenant_sub_theme_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('sub_theme_id')->constrained('sub_themes')->cascadeOnDelete();
            $table->boolean('is_allowed')->default(true); // true = force allow, false = force block
            $table->timestamps();

            $table->unique(['tenant_id', 'sub_theme_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_sub_theme_access');
        Schema::dropIfExists('tenant_feature_overrides');
        Schema::dropIfExists('subscriptions');
    }
};
