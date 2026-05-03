<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->onDelete('cascade');
            $table->string('key');
            $table->string('name')->nullable();
            $table->integer('position')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique(['page_id','key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sections');
    }
};
