<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Schema;

class Phase9Step3Test extends TestCase
{
    use RefreshDatabase;

    public function test_products_table_has_original_price()
    {
        $this->assertTrue(Schema::hasColumn('products', 'original_price'));
    }

    public function test_order_status_filtering()
    {
        $user = User::factory()->create();

        $order1 = Order::factory()->create(['user_id' => $user->id, 'status' => 'pending_payment']);
        $order2 = Order::factory()->create(['user_id' => $user->id, 'status' => 'completed']);

        $response = $this->actingAs($user)->getJson('/api/orders?status=pending_payment');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $order1->id])
            ->assertJsonMissing(['id' => $order2->id]);

        $responseAll = $this->actingAs($user)->getJson('/api/orders?status=all');
        $responseAll->assertJsonCount(2);
    }

    public function test_profile_update_handles_address_structure()
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'Test User',
            'city' => 'Taipei',
            'district' => 'Xinyi',
            'zip_code' => '110',
            'detail_address' => '101 Tower',
            'recipient_name' => 'Receiver',
            'phone' => '0912345678'
        ];

        $response = $this->actingAs($user)->putJson('/api/profile', $data);

        $response->assertStatus(200);

        // Verify Address Model created
        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'city' => 'Taipei',
            'district' => 'Xinyi',
            'zip_code' => '110',
            'detail_address' => '101 Tower',
            'is_default' => true
        ]);

        // Verify User Model sync (legacy column)
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'address' => '110 TaipeiXinyi101 Tower'
        ]);
    }
}
