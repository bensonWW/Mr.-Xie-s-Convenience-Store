<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Migrate Data
        $orders = DB::table('orders')->get();

        foreach ($orders as $order) {
            // Address
            DB::table('order_addresses')->insert([
                'order_id' => $order->id,
                'shipping_name' => $order->shipping_name ?? null,
                'shipping_phone' => $order->shipping_phone ?? null,
                'shipping_address' => $order->shipping_address ?? null,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ]);

            // Snapshot
            $snapshotData = [
                'member_level' => $order->snapshot_member_level ?? null,
                // Add any other legacy snapshot fields if they existed
            ];

            DB::table('order_snapshots')->insert([
                'order_id' => $order->id,
                'buyer_email' => $order->buyer_email ?? null,
                'snapshot_data' => json_encode($snapshotData),
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ]);
        }

        // 2. Drop Columns
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_name',
                'shipping_phone',
                'shipping_address',
                'snapshot_member_level',
                'buyer_email',
            ]);
        });
    }

    public function down(): void
    {
        // Add Columns Back
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_name')->nullable();
            $table->string('shipping_phone')->nullable();
            $table->string('shipping_address')->nullable();
            $table->string('snapshot_member_level')->nullable();
            $table->string('buyer_email')->nullable();
        });

        // Restore Data (Best Effort)
        $addresses = DB::table('order_addresses')->get()->keyBy('order_id');
        $snapshots = DB::table('order_snapshots')->get()->keyBy('order_id');

        foreach (DB::table('orders')->get() as $order) {
            $update = [];
            if (isset($addresses[$order->id])) {
                $addr = $addresses[$order->id];
                $update['shipping_name'] = $addr->shipping_name;
                $update['shipping_phone'] = $addr->shipping_phone;
                $update['shipping_address'] = $addr->shipping_address;
            }
            if (isset($snapshots[$order->id])) {
                $snap = $snapshots[$order->id];
                $update['buyer_email'] = $snap->buyer_email;
                $data = json_decode($snap->snapshot_data, true);
                if (isset($data['member_level'])) {
                    $update['snapshot_member_level'] = $data['member_level'];
                }
            }
            if (!empty($update)) {
                DB::table('orders')->where('id', $order->id)->update($update);
            }
        }
    }
};
