<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'applicable_days')) {
                $table->json('applicable_days')->nullable()->after('expire_date')->comment('Array of days: sunday, monday, etc.');
            }
            if (!Schema::hasColumn('coupons', 'start_time')) {
                $table->time('start_time')->nullable()->after('applicable_days')->comment('e.g. 12:30:00');
            }
            if (!Schema::hasColumn('coupons', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time')->comment('e.g. 15:30:00');
            }
            if (!Schema::hasColumn('coupons', 'time_slot')) {
                $table->string('time_slot')->nullable()->after('end_time')->comment('e.g. 12:30 - 15:30, lunch, happy_hours, custom');
            }
            if (!Schema::hasColumn('coupons', 'min_spend')) {
                $table->decimal('min_spend', 10, 2)->default(0)->after('time_slot');
            }
            if (!Schema::hasColumn('coupons', 'usage_limit')) {
                $table->integer('usage_limit')->nullable()->after('min_spend');
            }
            if (!Schema::hasColumn('coupons', 'used_count')) {
                $table->integer('used_count')->default(0)->after('usage_limit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn([
                'applicable_days',
                'start_time',
                'end_time',
                'time_slot',
                'min_spend',
                'usage_limit',
                'used_count',
            ]);
        });
    }
};
