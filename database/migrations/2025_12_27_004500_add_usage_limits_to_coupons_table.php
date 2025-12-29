<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds usage limits to prevent unlimited coupon abuse.
     */
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedInteger('usage_limit')
                ->nullable()
                ->after('ends_at')
                ->comment('Maximum number of times this coupon can be used (null = unlimited)');

            $table->unsignedInteger('usage_count')
                ->default(0)
                ->after('usage_limit')
                ->comment('Current number of times this coupon has been used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['usage_limit', 'usage_count']);
        });
    }
};
