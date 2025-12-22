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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'buyer_email')) {
                $table->string('buyer_email')->nullable()->after('user_id');
            }
        });

        // Drop snapshot_data if it exists (it may not exist on fresh install)
        if (Schema::hasColumn('orders', 'snapshot_data')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('snapshot_data');
            });
        }

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('order_items', 'options')) {
                $table->json('options')->nullable()->after('product_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('order_items', 'product_name')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn(['product_name', 'options']);
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'snapshot_data')) {
                $table->json('snapshot_data')->nullable();
            }
            if (Schema::hasColumn('orders', 'buyer_email')) {
                $table->dropColumn('buyer_email');
            }
        });
    }
};
