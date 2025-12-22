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

    public function test_revenue_chart_aggregates_orders_correctly()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@test.com']);

        // Clear cache to ensure fresh data
        Cache::forget('admin_stats');

        // 1. Create Orders in last 7 days (status: processing or completed counts as revenue)
        // Today: 100
        Order::create([
            'user_id' => $user->id,
            'status' => 'processing',
            'total_amount' => 100,
            'logistics_number' => 'LOGI-TEST-001',
        ]);

        // Yesterday: 200
        $this->travel(-1)->days();
        Order::create([
            'user_id' => $user->id,
            'status' => 'completed',
            'total_amount' => 200,
            'logistics_number' => 'LOGI-TEST-002',
        ]);
        $this->travelBack();

        // Clear cache again
        Cache::forget('admin_stats');

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

    public function test_total_sales_sums_paid_orders_only()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        // Clear cache
        Cache::forget('admin_stats');

        // Pending order (should NOT be counted)
        Order::create([
            'user_id' => $user->id,
            'status' => 'pending_payment',
            'total_amount' => 1000,
            'logistics_number' => 'LOGI-TEST-003',
        ]);

        // Processing order (SHOULD be counted - paid)
        Order::create([
            'user_id' => $user->id,
            'status' => 'processing',
            'total_amount' => 300,
            'logistics_number' => 'LOGI-TEST-004',
        ]);

        // Cancelled order (should NOT be counted)
        Order::create([
            'user_id' => $user->id,
            'status' => 'cancelled',
            'total_amount' => 500,
            'logistics_number' => 'LOGI-TEST-005',
        ]);

        // Clear cache again
        Cache::forget('admin_stats');

        $response = $this->actingAs($admin)->getJson('/api/admin/stats');

        // Only the 'processing' order (300) should count
        $this->assertEquals(300, $response->json('total_sales'));
    }
}
