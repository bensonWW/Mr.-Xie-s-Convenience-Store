<?php

namespace Tests;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ReproduceLogisticsIssue
{
    public function run()
    {
        echo "Starting reproduction test...\n";

        // 1. Create a User if not exists
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        // 2. Create an Order
        echo "Creating new order...\n";
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'processing',
            'total_amount' => 1000,
            'discount_amount' => 0,
            'shipping_fee' => 60,
            'snapshot_data' => [],
            // logistics_number should be auto-generated
        ]);

        echo "Order created. ID: {$order->id}\n";
        echo "Logistics Number: " . ($order->logistics_number ?? "NULL") . "\n";

        if (empty($order->logistics_number)) {
            echo "FAIL: Logistics number was not generated on create.\n";
        } else {
            echo "PASS: Logistics number generated.\n";
        }

        // 3. Test Update
        echo "Testing update...\n";
        $newNumber = "MANUAL-TEST-" . time();
        $order->update(['logistics_number' => $newNumber]);

        $order->refresh();
        echo "Updated Logistics Number: {$order->logistics_number}\n";

        if ($order->logistics_number === $newNumber) {
            echo "PASS: Update successful.\n";
        } else {
            echo "FAIL: Update failed.\n";
        }
    }
}
