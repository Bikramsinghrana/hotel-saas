<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->longText('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('status')->default('draft'); // published, draft
            $table->boolean('is_active')->default(true);
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['tenant_id', 'status', 'is_active']);
            $table->unique(['tenant_id', 'slug']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('blogs');
    }
};
