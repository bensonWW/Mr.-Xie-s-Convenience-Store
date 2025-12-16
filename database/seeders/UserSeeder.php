<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin User (VVIP)
        $admin = User::firstOrCreate(
            ['email' => 'admin@mrxie.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '0912-345-678',
                'status' => 'active',
                'created_at' => '2025-11-01 10:00:00',
            ]
        );

        // Create orders for Admin to make them VVIP (> $100,000)
        Order::create([
            'user_id' => $admin->id,
            'total_amount' => 201000,
            'status' => 'completed',
        ]);

        // 2. 吳宜峰 (General)
        $user1 = User::firstOrCreate(
            ['email' => 'feng@example.com'],
            [
                'name' => '吳宜峰',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '0988-123-456',
                'status' => 'active',
                'created_at' => '2025-11-20 14:30:00',
            ]
        );

        Order::create([
            'user_id' => $user1->id,
            'total_amount' => 26010,
            'status' => 'completed',
        ]);

        // 3. 陳小美 (General - Unverified/No Orders)
        User::firstOrCreate(
            ['email' => 'may@test.com'],
            [
                'name' => '陳小美',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '0911-000-999',
                'status' => 'active', // Design said "Unverified" but status col is active/banned. Keeping active for now.
                'created_at' => '2025-11-30 09:15:00',
            ]
        );

        // 4. 王大明 (Banned)
        $user3 = User::firstOrCreate(
            ['email' => 'wang@bad.com'],
            [
                'name' => '王大明',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '0900-111-222',
                'status' => 'banned',
                'created_at' => '2024-12-01 18:45:00',
            ]
        );

        Order::create([
            'user_id' => $user3->id,
            'total_amount' => 1500,
            'status' => 'cancelled', // Cancelled orders shouldn't count towards LTV
        ]);

        // Add a valid order for Wang to show some history if needed, but design says $1500 total.
        // If the design shows $1500 and status is banned, maybe they had a valid order before.
        Order::create([
            'user_id' => $user3->id,
            'total_amount' => 1500,
            'status' => 'completed',
        ]);

        // 5. Seed Wallet Transactions for UI Testing (Feng)
        // Initial Deposit
        \App\Models\WalletTransaction::create([
            'user_id' => $user1->id,
            'type' => 'deposit',
            'amount' => 5000,
            'description' => 'Opening Campaign Bonus',
            'created_at' => now()->subDays(10),
        ]);
        $user1->increment('balance', 5000);

        // Purchase
        \App\Models\WalletTransaction::create([
            'user_id' => $user1->id,
            'type' => 'payment',
            'amount' => -1200,
            'description' => 'Order #1001 Payment',
            'created_at' => now()->subDays(8),
        ]);
        $user1->decrement('balance', 1200);

        // Refund
        \App\Models\WalletTransaction::create([
            'user_id' => $user1->id,
            'type' => 'refund',
            'amount' => 1200,
            'description' => 'Order #1001 Refund',
            'created_at' => now()->subDays(5),
        ]);
        $user1->increment('balance', 1200);

        // Admin Adjustment (Withdraw)
        \App\Models\WalletTransaction::create([
            'user_id' => $user1->id,
            'type' => 'withdraw',
            'amount' => -200,
            'description' => 'System Correction',
            'created_at' => now()->subDays(2),
        ]);
        $user1->decrement('balance', 200);
    }
}
