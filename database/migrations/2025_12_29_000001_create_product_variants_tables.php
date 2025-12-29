<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add variant-related columns to products table
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('has_variants')->default(false)->after('stock');
            $table->integer('price_min')->nullable()->after('has_variants');
            $table->integer('price_max')->nullable()->after('price_min');
        });

        // 2. Create product_attributes table
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'display_order']);
        });

        // 3. Create product_attribute_values table
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('product_attributes')->cascadeOnDelete();
            $table->string('value', 100);
            $table->string('color_code', 7)->nullable(); // Hex color for swatches
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index(['attribute_id', 'display_order']);
        });

        // 4. Create product_variants table
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku', 50)->unique();
            $table->integer('price'); // In cents
            $table->integer('original_price')->nullable();
            $table->integer('stock')->default(0);
            $table->json('options'); // {attribute_id: value_id}
            $table->string('options_text', 255); // Human readable: "黑色 / 256GB"
            $table->string('options_hash', 64); // SHA256 hash for unique constraint
            $table->string('image', 255)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint on product + options combination (using hash)
            $table->unique(['product_id', 'options_hash'], 'unique_variant_combo');
            $table->index(['product_id', 'is_active']);
        });

        // 5. Add variant_id to cart_items
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('variant_id')->nullable()->after('product_id')
                ->constrained('product_variants')->nullOnDelete();
            $table->integer('snapshot_price')->nullable()->after('quantity'); // Price at add time
        });

        // 6. Add variant snapshot fields to order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            $table->string('variant_sku', 50)->nullable()->after('variant_id');
            $table->string('variant_options_text', 255)->nullable()->after('variant_sku');

            // Note: variant_id is not a strict FK because variants may be deleted
            // We keep the snapshot fields for historical accuracy
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['variant_id', 'variant_sku', 'variant_options_text']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
            $table->dropColumn(['variant_id', 'snapshot_price']);
        });

        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['has_variants', 'price_min', 'price_max']);
        });
    }
};
