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
        Schema::table('products', function (Blueprint $table) {
            // Used for filtering by category (e.g., Admin stats, Frontend filters)
            if (!Schema::hasIndex('products', 'products_category_index') && Schema::hasColumn('products', 'category')) {
                $table->index('category');
            }
            // Used for search
            if (!Schema::hasIndex('products', 'products_name_index')) {
                $table->index('name');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            // Used for Dashboard Last 7 Days charts (Date range queries)
            if (!Schema::hasIndex('orders', 'orders_created_at_index')) {
                $table->index('created_at');
            }
            // Used for Status tabs (Processing, Shipped, etc.)
            if (!Schema::hasIndex('orders', 'orders_status_index')) {
                $table->index('status');
            }
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Used for Revenue Stats (where type='payment')
            if (!Schema::hasIndex('wallet_transactions', 'wallet_transactions_type_index')) {
                $table->index('type');
            }
            // Used for date range aggregations
            if (!Schema::hasIndex('wallet_transactions', 'wallet_transactions_created_at_index')) {
                $table->index('created_at');
            }
        });

        // Add Soft Deletes if missing
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Check before dropping to avoid errors during rollback re-tries
            if (Schema::hasIndex('products', 'products_category_index')) $table->dropIndex(['category']);
            if (Schema::hasIndex('products', 'products_name_index')) $table->dropIndex(['name']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status']);
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'deleted_at')) $table->dropSoftDeletes();
        });
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'deleted_at')) $table->dropSoftDeletes();
        });
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'deleted_at')) $table->dropSoftDeletes();
        });
    }
};
