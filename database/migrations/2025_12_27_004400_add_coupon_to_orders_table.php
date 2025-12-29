<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds coupon_id foreign key to orders table to track which coupon was used.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')
                ->nullable()
                ->after('payment_method')
                ->constrained('coupons')
                ->nullOnDelete();

            $table->index('coupon_id', 'orders_coupon_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropIndex('orders_coupon_id_index');
            $table->dropColumn('coupon_id');
        });
    }
};
