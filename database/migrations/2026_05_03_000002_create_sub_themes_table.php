<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('sub_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_id')->constrained('themes')->cascadeOnDelete();
            $table->string('key');
            $table->string('name');
            $table->string('type')->default('single_hotel'); // single_hotel, multi_hotel, dine_in, takeaway, campus, etc.
            $table->string('preview_image')->nullable();
            $table->json('config_schema')->nullable(); // schema of supported color tokens, slider variants, hero layouts
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_premium')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['theme_id', 'key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_themes');
    }
};
