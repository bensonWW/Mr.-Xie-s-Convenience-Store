<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Don't use after() to avoid dependency on reference_id column
            if (!Schema::hasColumn('wallet_transactions', 'order_id')) {
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            }
            if (!Schema::hasColumn('wallet_transactions', 'refund_id')) {
                $table->foreignId('refund_id')->nullable()->constrained('orders')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('wallet_transactions', 'order_id')) {
                $table->dropForeign(['order_id']);
                $table->dropColumn('order_id');
            }
            if (Schema::hasColumn('wallet_transactions', 'refund_id')) {
                $table->dropForeign(['refund_id']);
                $table->dropColumn('refund_id');
            }
        });
    }
};
