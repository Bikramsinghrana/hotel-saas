<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('sub_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_id')->constrained('themes');
            $table->string('key');
            $table->string('name');
            $table->string('type');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['theme_id','key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_themes');
    }
};
