<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Services\WalletService;
use Illuminate\Support\Facades\Cache;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_revenue_chart_aggregates_payments_correctly()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@test.com']);
        $walletService = new WalletService();

        // Deposit enough funds first
        $walletService->deposit($user, 1000, 'Initial Deposit', 'init_1');

        // 1. Create Payments in last 7 days
        // Today: -100
        $walletService->pay($user, 100, 'Payment 1', 'order_1');

        // Yesterday: -200
        $this->travel(-1)->days();
        // Since we are creating transactions, created_at is automatic.
        $walletService->pay($user, 200, 'Payment 2', 'order_2');
        $this->travelBack();

        // Act
        $response = $this->actingAs($admin)->getJson('/api/admin/stats');

        // Assert
        $response->assertStatus(200);
        $chartData = $response->json('chart_data');

        // Today index is last
        $todayIndex = count($chartData['labels']) - 1;
        $yesterdayIndex = $todayIndex - 1;

        $this->assertEquals(100, $chartData['values'][$todayIndex]);
        $this->assertEquals(200, $chartData['values'][$yesterdayIndex]);
    }

    public function test_total_consumption_sums_payments_only()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $walletService = new WalletService();

        // Deposit 1000 (Should NOT be counted)
        $walletService->deposit($user, 1000, 'Topup', 'topup_1');

        // Spend 300 (Should be counted)
        $walletService->pay($user, 300, 'Buy Item', 'order_1');

        // Withdraw 50 (Should NOT be counted as consumption, it is a withdrawal)
        $walletService->withdraw($user, 50, 'Cash Out', 'withdraw_1');

        $response = $this->actingAs($admin)->getJson('/api/admin/stats');

        $this->assertEquals(300, $response->json('total_sales')); // total_sales mapped to Consumption
    }
}
