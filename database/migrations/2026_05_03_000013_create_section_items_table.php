<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->string('type')->default('content');
            $table->json('content')->nullable();
            $table->integer('position')->default(0);
            $table->foreignId('media_id')->nullable()->constrained('media');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('section_items');
    }
};
