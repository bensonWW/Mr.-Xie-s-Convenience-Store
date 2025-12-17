<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderSnapshotTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_preserves_user_data_in_snapshot()
    {
        // 1. Setup User with initial data
        $originalAddress = '123 Old St, Old City';
        $originalPhone = '0900000001';
        $user = User::factory()->create([
            'address' => $originalAddress,
            'phone' => $originalPhone,
            'balance' => 10000,
        ]);

        // 2. Setup Product
        $product = Product::factory()->create([
            'price' => 100,
            'stock' => 10,
        ]);

        // 3. Add to Cart
        $user->cart()->create()->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // 4. Create Order (Checkout)
        $response = $this->actingAs($user)->postJson('/api/orders');

        $response->assertStatus(201);
        $orderId = $response->json('id');

        // 5. Verify Snapshot immediately
        $order = Order::find($orderId);
        $this->assertNotNull($order->snapshot_data);
        $this->assertEquals($originalAddress, $order->snapshot_data['shipping_address']);
        $this->assertEquals($originalPhone, $order->snapshot_data['phone']);

        // 6. Change User Data
        $newAddress = '456 New Blvd, New City';
        $user->update([
            'address' => $newAddress,
            'phone' => '0999999999',
        ]);

        // 7. Verify Order Snapshot is UNCHANGED
        $order->refresh();
        $this->assertEquals($originalAddress, $order->snapshot_data['shipping_address']);
        $this->assertNotEquals($newAddress, $order->snapshot_data['shipping_address']);
    }
}
