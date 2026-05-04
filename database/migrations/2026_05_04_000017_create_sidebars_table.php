<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('sidebars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->longText('content')->nullable();
            $table->string('status')->default('draft'); // published, draft
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['tenant_id', 'status', 'is_active']);
            $table->unique(['tenant_id', 'slug']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sidebars');
    }
};
