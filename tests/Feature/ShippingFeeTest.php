<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\MemberLevel;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingFeeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed settings
        Setting::create(['key' => 'free_shipping_threshold', 'value' => 1000]);
        Setting::create(['key' => 'shipping_fee', 'value' => 60]);

        // Seed member levels
        MemberLevel::firstOrCreate(
            ['slug' => 'normal'],
            ['name' => 'Normal', 'threshold' => 0, 'discount' => 0.00]
        );
        MemberLevel::firstOrCreate(
            ['slug' => 'vip'],
            ['name' => 'VIP', 'threshold' => 1000, 'discount' => 0.05]
        );
    }

    public function test_shipping_fee_applied_below_threshold()
    {
        $user = User::factory()->create(['balance' => 5000]);
        $product = Product::factory()->create(['price' => 500, 'stock' => 10]);

        // Cart total 500 < 1000
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create(['product_id' => $product->id, 'quantity' => 1]);

        $response = $this->actingAs($user)->postJson('/api/orders');

        $response->assertStatus(201);

        $this->assertEquals(560, $response->json('total_amount')); // 500 + 60
        $this->assertEquals(60, $response->json('shipping_fee'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_amount' => 560,
            'shipping_fee' => 60,
        ]);
    }

    public function test_free_shipping_above_threshold()
    {
        $user = User::factory()->create(['balance' => 5000]);
        $product = Product::factory()->create(['price' => 1200, 'stock' => 10]);

        // Cart total 1200 >= 1000
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create(['product_id' => $product->id, 'quantity' => 1]);

        $response = $this->actingAs($user)->postJson('/api/orders');

        $response->assertStatus(201);
        $this->assertEquals(1200, $response->json('total_amount')); // 1200 + 0
        $this->assertEquals(0, $response->json('shipping_fee'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_amount' => 1200,
            'shipping_fee' => 0,
        ]);
    }

    public function test_shipping_fee_with_member_discount()
    {
        // VIP User (5% off)
        $vipLevel = MemberLevel::where('slug', 'vip')->first();
        $this->assertNotNull($vipLevel, 'VIP level should exist');
        $this->assertEquals(0.05, $vipLevel->discount);

        $user = User::factory()->create(['balance' => 5000, 'member_level_id' => $vipLevel->id]);
        $this->assertEquals($vipLevel->id, $user->member_level_id);

        // Reload user to get fresh relationship
        $user->refresh();
        $this->assertNotNull($user->memberLevel, 'User should have member level loaded');
        $this->assertEquals(0.05, $user->memberLevel->discount);

        // Product price 1000. 
        // Discount 50 (5%). Net 950.
        // Threshold 1000.
        // Net 950 < 1000 -> Should add shipping fee 60.
        // Total = 950 + 60 = 1010

        $product = Product::factory()->create(['price' => 1000, 'stock' => 10]);

        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create(['product_id' => $product->id, 'quantity' => 1]);

        $response = $this->actingAs($user)->postJson('/api/orders');

        $response->assertStatus(201);

        $order = $response->json();

        $this->assertEquals(1010, $order['total_amount'], 'Total: subtotal 1000 - discount 50 + shipping 60 = 1010');
        $this->assertEquals(60, $order['shipping_fee'], 'Shipping fee should be 60 since net < threshold');
        $this->assertEquals(50, $order['discount_amount'], 'Discount should be 5% of 1000 = 50');
    }
}
