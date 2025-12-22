<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add missing performance indexes identified in code review.
 * These indexes optimize common query patterns.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Index for MemberLevelService::checkAndUpgrade() query
            // SELECT SUM(total_amount) WHERE user_id = ? AND status IN (...)
            $table->index(['user_id', 'status'], 'orders_user_status_idx');

            // Index for admin dashboard queries
            // SELECT ... WHERE status = ? ORDER BY created_at
            $table->index(['status', 'created_at'], 'orders_status_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_status_idx');
            $table->dropIndex('orders_status_created_idx');
        });
    }
};
