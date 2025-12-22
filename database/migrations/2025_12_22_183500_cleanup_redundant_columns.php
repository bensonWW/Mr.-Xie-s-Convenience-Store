<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * 3NF Cleanup: Remove redundant columns that have been normalized.
 * 
 * - users.member_level: Replaced by member_level_id FK
 * - products.category: Replaced by category_id FK
 * 
 * Note: SQLite does not support DROP COLUMN, so this migration is skipped for SQLite.
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        // SQLite doesn't support DROP COLUMN - skip for test environment
        if ($driver === 'sqlite') {
            return;
        }

        // Drop redundant member_level string column from users
        if (Schema::hasColumn('users', 'member_level')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('member_level');
            });
        }

        // Drop redundant category string column from products
        if (Schema::hasColumn('products', 'category')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        // Skip for SQLite
        if ($driver === 'sqlite') {
            return;
        }

        // Restore member_level column
        if (!Schema::hasColumn('users', 'member_level')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('member_level')->default('normal')->after('status');
            });
        }

        // Restore category column
        if (!Schema::hasColumn('products', 'category')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('category')->default('general')->after('image');
            });
        }
    }
};
