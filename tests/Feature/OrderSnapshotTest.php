<?php

namespace Tests\Feature;

use App\Models\MemberLevel;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderSnapshotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed normal level for MemberLevelService fallback
        MemberLevel::firstOrCreate(
            ['slug' => 'normal'],
            ['name' => 'Normal', 'threshold' => 0, 'discount' => 0.00]
        );
    }

    public function test_order_preserves_user_data_in_snapshot()
    {
        // 1. Setup User with initial data
        $originalAddress = '123 Old St, Old City';
        $originalPhone = '0900000001';
        $originalEmail = 'test@example.com';
        $user = User::factory()->create([
            'phone' => $originalPhone,
            'email' => $originalEmail,
            'balance' => 10000,
        ]);

        // Create default address in normalized addresses table
        $user->addresses()->create([
            'city' => 'Old City',
            'district' => 'Old District',
            'zip_code' => '100',
            'detail_address' => '123 Old St',
            'recipient_name' => $user->name,
            'phone' => $originalPhone,
            'is_default' => true,
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
        $this->assertNotNull($order->address);
        $this->assertEquals($originalPhone, $order->address->phone);
        $this->assertEquals($originalEmail, $order->snapshot->buyer_email);

        // 6. Change User Data in addresses table
        $user->addresses()->where('is_default', true)->update([
            'detail_address' => '456 New Blvd',
            'phone' => '0999999999',
        ]);
        $user->update([
            'email' => 'new@example.com',
        ]);

        // 7. Verify Order Snapshot is UNCHANGED
        $order->refresh();
        $this->assertEquals($originalPhone, $order->address->phone);

        $this->assertEquals($originalEmail, $order->snapshot->buyer_email);
        $this->assertNotEquals('new@example.com', $order->snapshot->buyer_email);
    }
}
