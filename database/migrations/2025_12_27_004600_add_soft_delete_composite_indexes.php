<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds composite indexes for soft delete columns to improve query performance.
     * Without these indexes, WHERE deleted_at IS NULL causes full table scans.
     */
    public function up(): void
    {
        // Users: deleted_at + email (common query: find active user by email)
        Schema::table('users', function (Blueprint $table) {
            $table->index(['deleted_at', 'email'], 'users_deleted_at_email_index');
        });

        // Products: deleted_at + status (common query: find active products by status)
        Schema::table('products', function (Blueprint $table) {
            $table->index(['deleted_at', 'status'], 'products_deleted_at_status_index');
        });

        // Orders: deleted_at + user_id (common query: find user's non-deleted orders)
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['deleted_at', 'user_id'], 'orders_deleted_at_user_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_deleted_at_email_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_deleted_at_status_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_deleted_at_user_id_index');
        });
    }
};
