<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update cart_items unique constraint to include variant_id.
     * This allows the same product with different variants to be added separately.
     * 
     * MySQL issue: The unique index may be used by a foreign key, so we need to:
     * 1. Drop the FK constraints first
     * 2. Drop the old unique index
     * 3. Add the new unique index
     * 4. Re-add the FK constraints
     */
    public function up(): void
    {
        // Clean up duplicate entries first (keeping highest quantity)
        $this->cleanupDuplicateCartItems();

        Schema::table('cart_items', function (Blueprint $table) {
            // Drop foreign keys first (MySQL requires this before dropping the unique index)
            $table->dropForeign(['cart_id']);
            $table->dropForeign(['product_id']);

            // Drop old unique constraint
            $table->dropUnique('cart_items_cart_product_unique');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            // Add new unique constraint including variant_id
            // Note: variant_id can be null for products without variants
            $table->unique(['cart_id', 'product_id', 'variant_id'], 'cart_items_cart_product_variant_unique');

            // Re-add foreign key constraints
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Remove duplicates keeping highest quantity for each cart+product+variant combo.
     */
    private function cleanupDuplicateCartItems(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite: Keep highest ID per combo
            DB::statement("
                DELETE FROM cart_items 
                WHERE id NOT IN (
                    SELECT MAX(id) 
                    FROM cart_items 
                    GROUP BY cart_id, product_id, variant_id
                )
            ");
        } else {
            // MySQL: Delete duplicates keeping highest ID
            DB::statement("
                DELETE ci1 FROM cart_items ci1
                INNER JOIN cart_items ci2
                WHERE ci1.id < ci2.id
                  AND ci1.cart_id = ci2.cart_id
                  AND ci1.product_id = ci2.product_id
                  AND (ci1.variant_id = ci2.variant_id OR (ci1.variant_id IS NULL AND ci2.variant_id IS NULL))
            ");
        }
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['cart_id']);
            $table->dropForeign(['product_id']);

            $table->dropUnique('cart_items_cart_product_variant_unique');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['cart_id', 'product_id'], 'cart_items_cart_product_unique');

            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
};
