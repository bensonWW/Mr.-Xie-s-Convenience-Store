<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add missing indexes identified during code review:
     * - Composite unique index on cart_items to prevent duplicate products in cart
     * - Composite unique index on favorites to prevent duplicate favorites
     * - Index on wallet_transactions.order_id for efficient order-based queries
     */
    public function up(): void
    {
        // cart_items: unique constraint to prevent duplicate products in the same cart
        Schema::table('cart_items', function (Blueprint $table) {
            // First, clean up any existing duplicates (keep the one with highest quantity)
            $this->cleanupDuplicateCartItems();

            $table->unique(['cart_id', 'product_id'], 'cart_items_cart_product_unique');
        });

        // favorites: unique constraint to prevent duplicate favorites
        Schema::table('favorites', function (Blueprint $table) {
            // Clean up any existing duplicates
            $this->cleanupDuplicateFavorites();

            $table->unique(['user_id', 'product_id'], 'favorites_user_product_unique');
        });

        // wallet_transactions: index on order_id for efficient queries
        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('wallet_transactions', 'order_id')) {
                $table->index('order_id', 'wallet_transactions_order_id_index');
            }
        });
    }

    /**
     * Remove duplicates from cart_items, keeping the one with highest quantity.
     */
    private function cleanupDuplicateCartItems(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite approach: delete duplicates keeping highest ID
            DB::statement("
                DELETE FROM cart_items 
                WHERE id NOT IN (
                    SELECT MAX(id) 
                    FROM cart_items 
                    GROUP BY cart_id, product_id
                )
            ");
        } else {
            // MySQL approach
            DB::statement("
                DELETE ci1 FROM cart_items ci1
                INNER JOIN cart_items ci2
                WHERE ci1.id < ci2.id
                  AND ci1.cart_id = ci2.cart_id
                  AND ci1.product_id = ci2.product_id
            ");
        }
    }

    /**
     * Remove duplicates from favorites, keeping the oldest one.
     */
    private function cleanupDuplicateFavorites(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement("
                DELETE FROM favorites 
                WHERE id NOT IN (
                    SELECT MIN(id) 
                    FROM favorites 
                    GROUP BY user_id, product_id
                )
            ");
        } else {
            DB::statement("
                DELETE f1 FROM favorites f1
                INNER JOIN favorites f2
                WHERE f1.id > f2.id
                  AND f1.user_id = f2.user_id
                  AND f1.product_id = f2.product_id
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('cart_items_cart_product_unique');
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->dropUnique('favorites_user_product_unique');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('wallet_transactions', 'order_id')) {
                $table->dropIndex('wallet_transactions_order_id_index');
            }
        });
    }
};
