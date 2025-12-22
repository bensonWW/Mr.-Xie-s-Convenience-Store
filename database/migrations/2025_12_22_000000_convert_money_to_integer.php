<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Convert monetary columns from DECIMAL to INTEGER (cents).
 * 
 * This migration handles both:
 * - Fresh installs: Just changes column types
 * - Existing data: Multiplies values by 100 before type change
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Users Balance
        if (DB::table('users')->count() > 0) {
            DB::table('users')->update(['balance' => DB::raw('balance * 100')]);
        }
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('balance')->default(0)->change();
        });

        // 2. Products Price & Original Price
        if (DB::table('products')->count() > 0) {
            DB::table('products')->update([
                'price' => DB::raw('price * 100'),
                'original_price' => DB::raw('IFNULL(original_price * 100, NULL)')
            ]);
        }
        Schema::table('products', function (Blueprint $table) {
            $table->bigInteger('price')->change();
            $table->bigInteger('original_price')->nullable()->change();
        });

        // 3. Orders: total_amount, shipping_fee, discount_amount
        if (DB::table('orders')->count() > 0) {
            DB::table('orders')->update([
                'total_amount' => DB::raw('total_amount * 100'),
                'shipping_fee' => DB::raw('IFNULL(shipping_fee * 100, 0)'),
                'discount_amount' => DB::raw('IFNULL(discount_amount * 100, 0)')
            ]);
        }
        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger('total_amount')->change();
            $table->bigInteger('shipping_fee')->default(0)->change();
            $table->bigInteger('discount_amount')->default(0)->change();
        });

        // 4. Order Items: price
        if (DB::table('order_items')->count() > 0) {
            DB::table('order_items')->update(['price' => DB::raw('price * 100')]);
        }
        Schema::table('order_items', function (Blueprint $table) {
            $table->bigInteger('price')->change();
        });

        // 5. Wallet Transactions: amount
        if (DB::table('wallet_transactions')->count() > 0) {
            DB::table('wallet_transactions')->update(['amount' => DB::raw('amount * 100')]);
        }
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->bigInteger('amount')->change();
        });
    }

    public function down(): void
    {
        // Reverse Process: Divide by 100 and change back to DECIMAL(10,2)

        // 1. Users
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('balance', 10, 2)->default(0.00)->change();
        });
        if (DB::table('users')->count() > 0) {
            DB::table('users')->update(['balance' => DB::raw('balance / 100')]);
        }

        // 2. Products
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
            $table->decimal('original_price', 10, 2)->nullable()->change();
        });
        if (DB::table('products')->count() > 0) {
            DB::table('products')->update([
                'price' => DB::raw('price / 100'),
                'original_price' => DB::raw('original_price / 100')
            ]);
        }

        // 3. Orders
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_amount', 10, 2)->change();
            $table->decimal('shipping_fee', 10, 2)->default(0)->change();
            $table->decimal('discount_amount', 10, 2)->default(0)->change();
        });
        if (DB::table('orders')->count() > 0) {
            DB::table('orders')->update([
                'total_amount' => DB::raw('total_amount / 100'),
                'shipping_fee' => DB::raw('shipping_fee / 100'),
                'discount_amount' => DB::raw('discount_amount / 100')
            ]);
        }

        // 4. Order Items
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
        });
        if (DB::table('order_items')->count() > 0) {
            DB::table('order_items')->update(['price' => DB::raw('price / 100')]);
        }

        // 5. Wallet Transactions
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
        if (DB::table('wallet_transactions')->count() > 0) {
            DB::table('wallet_transactions')->update(['amount' => DB::raw('amount / 100')]);
        }
    }
};
