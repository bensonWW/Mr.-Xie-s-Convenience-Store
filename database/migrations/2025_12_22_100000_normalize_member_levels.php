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
     * This migration normalizes the member_level system to comply with 3NF.
     * It creates a member_levels table and migrates the string-based member_level
     * column in users to a proper foreign key reference.
     */
    public function up(): void
    {
        // 1. Create member_levels table
        Schema::create('member_levels', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();  // 'normal', 'vip', 'platinum'
            $table->string('name', 100);           // '一般會員', 'VIP 會員'
            $table->integer('threshold')->default(0);  // Upgrade threshold in cents
            $table->decimal('discount', 5, 4)->default(0.0000);  // 0.05 = 5%
            $table->timestamps();
        });

        // 2. Seed member_levels from config
        $levels = config('shop.levels', [
            'normal' => ['name' => '一般會員', 'threshold' => 0, 'discount' => 0.00],
            'vip' => ['name' => 'VIP 會員', 'threshold' => 1000, 'discount' => 0.05],
            'platinum' => ['name' => '白金會員', 'threshold' => 5000, 'discount' => 0.10],
        ]);

        $levelIdMap = [];
        foreach ($levels as $slug => $data) {
            $id = DB::table('member_levels')->insertGetId([
                'slug' => $slug,
                'name' => $data['name'] ?? ucfirst($slug),
                'threshold' => $data['threshold'] ?? 0,
                'discount' => $data['discount'] ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $levelIdMap[$slug] = $id;
        }

        // 3. Add member_level_id column to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('member_level_id')->nullable()->after('member_level');
        });

        // 4. Migrate existing member_level strings to member_level_id
        $users = DB::table('users')->get(['id', 'member_level']);
        foreach ($users as $user) {
            $slug = $user->member_level ?? 'normal';
            $levelId = $levelIdMap[$slug] ?? $levelIdMap['normal'] ?? null;

            if ($levelId) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['member_level_id' => $levelId]);
            }
        }

        // 5. Add foreign key constraint
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('member_level_id')
                ->references('id')
                ->on('member_levels')
                ->nullOnDelete();
        });

        // 6. Drop old member_level string column (skip for SQLite)
        $driver = DB::connection()->getDriverName();
        if ($driver !== 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('member_level');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        // 1. Add back member_level string column if dropped
        if ($driver !== 'sqlite' && !Schema::hasColumn('users', 'member_level')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('member_level')->default('normal')->after('status');
            });
        }

        // 2. Migrate member_level_id back to string
        if (Schema::hasColumn('users', 'member_level')) {
            $levels = DB::table('member_levels')->get()->keyBy('id');

            foreach (DB::table('users')->get(['id', 'member_level_id']) as $user) {
                if ($user->member_level_id && isset($levels[$user->member_level_id])) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['member_level' => $levels[$user->member_level_id]->slug]);
                }
            }
        }

        // 3. Drop member_level_id column
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'member_level_id')) {
                $table->dropForeign(['member_level_id']);
                $table->dropColumn('member_level_id');
            }
        });

        // 4. Drop member_levels table
        Schema::dropIfExists('member_levels');
    }
};
