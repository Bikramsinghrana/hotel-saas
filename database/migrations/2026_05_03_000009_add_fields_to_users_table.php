<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id');
            $table->foreignId('tenant_id')->nullable()->after('role_id')->constrained('tenants');
            $table->foreignId('theme_id')->nullable()->after('tenant_id')->constrained('themes');
            $table->string('phone')->nullable()->after('email');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['theme_id']);
            $table->dropColumn(['role_id','tenant_id','theme_id','phone']);
        });
    }
};
