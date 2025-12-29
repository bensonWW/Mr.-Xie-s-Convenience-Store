<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add composite indexes to improve query performance.
 * 
 * These indexes address Code Review findings:
 * 1. order_items(order_id, product_id) - for order item lookups
 * 2. wallet_transactions(user_id, type) - for transaction history filtering
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Composite index for queries like: WHERE order_id = ? AND product_id = ?
            $table->index(['order_id', 'product_id'], 'order_items_order_product_idx');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Composite index for queries like: WHERE user_id = ? AND type = ?
            $table->index(['user_id', 'type'], 'wallet_transactions_user_type_idx');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_order_product_idx');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex('wallet_transactions_user_type_idx');
        });
    }
};
