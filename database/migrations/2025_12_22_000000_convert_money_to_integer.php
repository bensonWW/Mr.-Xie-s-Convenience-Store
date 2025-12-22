<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Users Balance
        // Update data: multiply by 100 to convert to cents
        DB::table('users')->update(['balance' => DB::raw('balance * 100')]);
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('balance')->default(0)->change();
        });

        // 2. Products Price & Original Price
        DB::table('products')->update([
            'price' => DB::raw('price * 100'),
            'original_price' => DB::raw('original_price * 100')
        ]);
        Schema::table('products', function (Blueprint $table) {
            $table->bigInteger('price')->change();
            $table->bigInteger('original_price')->nullable()->change();
        });

        // 3. Orders: total_amount, shipping_fee, discount_amount
        // Note: Make sure to handle nulls if any, though schema suggests some are not nullable.
        DB::table('orders')->update([
            'total_amount' => DB::raw('total_amount * 100'),
            'shipping_fee' => DB::raw('shipping_fee * 100'),
            'discount_amount' => DB::raw('discount_amount * 100')
        ]);
        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger('total_amount')->change();
            $table->bigInteger('shipping_fee')->default(0)->change();
            $table->bigInteger('discount_amount')->default(0)->change();
        });

        // 4. Order Items: price
        DB::table('order_items')->update(['price' => DB::raw('price * 100')]);
        Schema::table('order_items', function (Blueprint $table) {
            $table->bigInteger('price')->change();
        });

        // 5. Wallet Transactions: amount
        DB::table('wallet_transactions')->update(['amount' => DB::raw('amount * 100')]);
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->bigInteger('amount')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse Process: Divide by 100 and change back to DECIMAL(10,2)

        // 1. Users
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('balance', 10, 2)->default(0.00)->change();
        });
        DB::table('users')->update(['balance' => DB::raw('balance / 100')]);

        // 2. Products
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
            $table->decimal('original_price', 10, 2)->nullable()->change();
        });
        DB::table('products')->update([
            'price' => DB::raw('price / 100'),
            'original_price' => DB::raw('original_price / 100')
        ]);

        // 3. Orders
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_amount', 10, 2)->change();
            $table->decimal('shipping_fee', 10, 2)->default(0)->change();
            $table->decimal('discount_amount', 10, 2)->default(0)->change();
        });
        DB::table('orders')->update([
            'total_amount' => DB::raw('total_amount / 100'),
            'shipping_fee' => DB::raw('shipping_fee / 100'),
            'discount_amount' => DB::raw('discount_amount / 100')
        ]);

        // 4. Order Items
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
        });
        DB::table('order_items')->update(['price' => DB::raw('price / 100')]);

        // 5. Wallet Transactions
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
        DB::table('wallet_transactions')->update(['amount' => DB::raw('amount / 100')]);
    }
};
