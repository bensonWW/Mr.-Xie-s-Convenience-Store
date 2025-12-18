<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Schema;

class Phase9Step4Test extends TestCase
{
    use RefreshDatabase;

    public function test_orders_table_has_new_columns()
    {
        $this->assertTrue(Schema::hasColumns('orders', [
            'logistics_number',
            'payment_method'
        ]));
    }

    public function test_order_auto_generates_logistics_and_defaults_payment()
    {
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 1000,
            'status' => 'pending_payment',
            'snapshot_data' => []
        ]);

        $this->assertNotNull($order->logistics_number);
        $this->assertStringStartsWith('LOGI-' . date('Ymd') . '-', $order->logistics_number);
        $this->assertEquals('wallet', $order->payment_method);
    }

    public function test_logistics_number_is_unique()
    {
        $user = User::factory()->create();

        $order1 = Order::create([
            'user_id' => $user->id,
            'total_amount' => 100,
            'status' => 'processing',
            'snapshot_data' => []
        ]);

        $order2 = Order::create([
            'user_id' => $user->id,
            'total_amount' => 200,
            'status' => 'processing',
            'snapshot_data' => []
        ]);

        $this->assertNotEquals($order1->logistics_number, $order2->logistics_number);
    }
}
