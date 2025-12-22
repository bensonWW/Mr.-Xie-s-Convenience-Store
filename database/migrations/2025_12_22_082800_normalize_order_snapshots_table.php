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
     * This migration normalizes the order_snapshots table to comply with 1NF
     * by converting the JSON 'data' column into atomic columns.
     */
    public function up(): void
    {
        // 1. Add new atomic columns
        Schema::table('order_snapshots', function (Blueprint $table) {
            $table->string('buyer_email')->nullable()->after('order_id');
            $table->string('member_level_name')->nullable()->after('buyer_email');
            $table->string('user_name')->nullable()->after('member_level_name');
        });

        // 2. Migrate data from JSON column to new columns (if data column exists)
        if (Schema::hasColumn('order_snapshots', 'data')) {
            $snapshots = DB::table('order_snapshots')->whereNotNull('data')->get();

            foreach ($snapshots as $snapshot) {
                $data = json_decode($snapshot->data, true);

                if (is_array($data)) {
                    DB::table('order_snapshots')
                        ->where('id', $snapshot->id)
                        ->update([
                            'buyer_email' => $data['buyer_email'] ?? null,
                            'member_level_name' => $data['member_level'] ?? null,
                            'user_name' => $data['user_name'] ?? null,
                        ]);
                }
            }

            // 3. Drop the old JSON column (skip for SQLite)
            $driver = DB::connection()->getDriverName();
            if ($driver !== 'sqlite') {
                Schema::table('order_snapshots', function (Blueprint $table) {
                    $table->dropColumn('data');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        // 1. Add back the JSON column if it was dropped
        if ($driver !== 'sqlite' && !Schema::hasColumn('order_snapshots', 'data')) {
            Schema::table('order_snapshots', function (Blueprint $table) {
                $table->json('data')->nullable()->after('order_id');
            });
        }

        // 2. Migrate data back to JSON (if data column exists)
        if (Schema::hasColumn('order_snapshots', 'data')) {
            $snapshots = DB::table('order_snapshots')->get();

            foreach ($snapshots as $snapshot) {
                $data = [
                    'buyer_email' => $snapshot->buyer_email,
                    'member_level' => $snapshot->member_level_name,
                    'user_name' => $snapshot->user_name,
                ];

                DB::table('order_snapshots')
                    ->where('id', $snapshot->id)
                    ->update(['data' => json_encode($data)]);
            }
        }

        // 3. Drop the atomic columns
        Schema::table('order_snapshots', function (Blueprint $table) {
            $columnsToCheck = ['buyer_email', 'member_level_name', 'user_name'];
            $columnsToDrop = [];

            foreach ($columnsToCheck as $col) {
                if (Schema::hasColumn('order_snapshots', $col)) {
                    $columnsToDrop[] = $col;
                }
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
