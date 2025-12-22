<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration removes the ambiguous reference_id column from wallet_transactions.
     * We now have explicit order_id and refund_id foreign keys.
     */
    public function up(): void
    {
        // Only drop on non-SQLite databases
        $driver = DB::connection()->getDriverName();

        if ($driver !== 'sqlite' && Schema::hasColumn('wallet_transactions', 'reference_id')) {
            Schema::table('wallet_transactions', function (Blueprint $table) {
                $table->dropColumn('reference_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver !== 'sqlite' && !Schema::hasColumn('wallet_transactions', 'reference_id')) {
            Schema::table('wallet_transactions', function (Blueprint $table) {
                $table->string('reference_id')->nullable()->after('amount');
            });
        }
    }
};
