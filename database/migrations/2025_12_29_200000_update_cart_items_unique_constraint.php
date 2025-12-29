<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Update cart_items unique constraint to include variant_id.
     * This allows the same product with different variants to be added separately.
     */
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Drop old unique constraint (cart_id + product_id only)
            $table->dropUnique('cart_items_cart_product_unique');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            // Add new unique constraint including variant_id
            // Note: variant_id can be null for products without variants
            $table->unique(['cart_id', 'product_id', 'variant_id'], 'cart_items_cart_product_variant_unique');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('cart_items_cart_product_variant_unique');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['cart_id', 'product_id'], 'cart_items_cart_product_unique');
        });
    }
};
