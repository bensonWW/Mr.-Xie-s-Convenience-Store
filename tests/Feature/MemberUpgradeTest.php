<?php

namespace Tests\Feature;

use App\Models\MemberLevel;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberUpgradeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create member levels with test-appropriate thresholds
        MemberLevel::firstOrCreate(
            ['slug' => 'normal'],
            ['name' => 'Normal', 'threshold' => 0, 'discount' => 0.00]
        );
        MemberLevel::firstOrCreate(
            ['slug' => 'vip'],
            ['name' => 'VIP', 'threshold' => 1000, 'discount' => 0.05]
        );
    }

    public function test_user_upgrades_to_vip_after_spending_threshold()
    {
        // 1. Arrange: User with Normal level and enough balance
        $normalLevel = MemberLevel::where('slug', 'normal')->first();
        $user = User::factory()->create([
            'member_level_id' => $normalLevel->id,
            'balance' => 10000
        ]);
        $this->actingAs($user);

        // Product cost 2000 > 1000 (VIP Threshold)
        $product = Product::factory()->create(['price' => 2000, 'stock' => 10]);

        // Add to cart
        $this->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // 2. Act: Checkout
        $response = $this->postJson('/api/orders');
        $response->assertStatus(201);

        // 3. Mark as Completed
        $orderId = $response->json('id');
        $order = Order::find($orderId);
        $order->update(['status' => 'completed']);

        // Manually trigger upgrade check
        $service = new \App\Services\MemberLevelService();
        $service->checkAndUpgrade($user);

        // 4. Assert
        $user->refresh();
        $this->assertEquals('vip', $user->member_level_slug);
    }

    public function test_vip_user_gets_discount()
    {
        // 1. Arrange: VIP User
        $vipLevel = MemberLevel::where('slug', 'vip')->first();
        $user = User::factory()->create([
            'member_level_id' => $vipLevel->id,
            'balance' => 10000
        ]);
        $this->actingAs($user);

        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

        $this->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // 2. Act: Checkout
        $response = $this->postJson('/api/orders');
        $response->assertStatus(201);

        // 3. Assert
        // Total should be: 100 - 5% = 95, plus shipping fee (60) = 155
        // discount_amount is just the member discount, not including shipping
        $this->assertEquals(155, $response->json('total_amount')); // 95 + 60 shipping
        $this->assertEquals(5, $response->json('discount_amount'));
    }
}
