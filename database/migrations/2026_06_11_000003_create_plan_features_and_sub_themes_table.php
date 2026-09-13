<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained('features')->cascadeOnDelete();
            $table->string('limit_value')->nullable(); // e.g. "50", "unlimited", "true"
            $table->timestamps();

            $table->unique(['plan_id', 'feature_id']);
        });

        Schema::create('plan_sub_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->foreignId('sub_theme_id')->constrained('sub_themes')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['plan_id', 'sub_theme_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_sub_themes');
        Schema::dropIfExists('plan_features');
    }
};
