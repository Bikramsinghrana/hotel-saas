<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')->nullable();
            $table->string('room_type');
            $table->string('image_url')->nullable();

            $table->enum('status', ['pending', 'draft', 'active', 'inactive'])
                ->default('draft')
                ->index();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_types');
    }
};
