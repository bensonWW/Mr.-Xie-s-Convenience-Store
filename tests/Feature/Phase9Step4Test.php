<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Schema;
use App\Services\OrderSequenceGenerator;

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

    public function test_order_can_be_created_with_logistics_number()
    {
        $user = User::factory()->create();
        $generator = app(OrderSequenceGenerator::class);
        $logisticsNumber = $generator->generateLogisticsNumber();

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 1000,
            'status' => 'pending_payment',
            'logistics_number' => $logisticsNumber,
            'payment_method' => 'wallet',
        ]);

        $this->assertNotNull($order->logistics_number);
        $this->assertStringStartsWith('LOGI-' . date('Ymd') . '-', $order->logistics_number);
        $this->assertEquals('wallet', $order->payment_method);
    }

    public function test_logistics_number_is_unique()
    {
        $user = User::factory()->create();
        $generator = app(OrderSequenceGenerator::class);

        $order1 = Order::create([
            'user_id' => $user->id,
            'total_amount' => 100,
            'status' => 'processing',
            'logistics_number' => $generator->generateLogisticsNumber(),
        ]);

        $order2 = Order::create([
            'user_id' => $user->id,
            'total_amount' => 200,
            'status' => 'processing',
            'logistics_number' => $generator->generateLogisticsNumber(),
        ]);

        $this->assertNotEquals($order1->logistics_number, $order2->logistics_number);
    }
}
