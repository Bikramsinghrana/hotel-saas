<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('navigations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('title'); // e.g., 'Home', 'About', 'Rooms'
            $table->text('content')->nullable(); // Description or additional content
            $table->string('url'); // URL slug or full path
            $table->integer('order')->default(0); // For sorting
            $table->boolean('is_active')->default(true); // Show/hide nav item
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('navigations');
    }
};
