<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain')->unique();
            $table->foreignId('theme_id')->nullable()->constrained('themes')->nullOnDelete();
            $table->foreignId('sub_theme_id')->nullable()->constrained('sub_themes')->nullOnDelete();
            $table->json('theme_config')->nullable(); // Runtime UI customizations: primary_color, secondary_color, font, sliders, etc.
            $table->json('settings')->nullable();
            $table->json('address')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenants');
    }
};
