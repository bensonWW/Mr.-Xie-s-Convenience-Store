<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 0. Enforce Schema Correctness (Drop and Recreate to match 3NF plan)
        // This handles cases where previous migrations ran with old schema
        Schema::dropIfExists('order_addresses');
        Schema::dropIfExists('order_snapshots');

        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });

        Schema::create('order_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->json('data')->nullable();
            $table->timestamps();
        });

        // 1. Move data
        $orders = DB::table('orders')->get();
        foreach ($orders as $order) {
            // Provide default values just in case, though schema allows nulls but we prefer data
            // Check if columns exist before accessing (though valid for this specific project point)
            $shippingName = $order->shipping_name ?? null;
            $shippingPhone = $order->shipping_phone ?? null;
            $shippingAddress = $order->shipping_address ?? null;

            if ($shippingName || $shippingPhone || $shippingAddress) {
                DB::table('order_addresses')->insert([
                    'order_id' => $order->id,
                    'name' => $shippingName,
                    'phone' => $shippingPhone,
                    'address' => $shippingAddress,
                    'created_at' => $order->created_at ?? now(),
                    'updated_at' => $order->updated_at ?? now(),
                ]);
            }

            // Create empty snapshot or move data if available
            DB::table('order_snapshots')->insert([
                'order_id' => $order->id,
                'data' => json_encode([]), // Backfill empty for legacy
                'created_at' => $order->created_at ?? now(),
                'updated_at' => $order->updated_at ?? now(),
            ]);
        }

        // 2. Drop columns safely
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'shipping_name')) {
                $table->dropColumn('shipping_name');
            }
            if (Schema::hasColumn('orders', 'shipping_phone')) {
                $table->dropColumn('shipping_phone');
            }
            if (Schema::hasColumn('orders', 'shipping_address')) {
                $table->dropColumn('shipping_address');
            }

            // Drop snapshot_data if it exists
            if (Schema::hasColumn('orders', 'snapshot_data')) {
                $table->dropColumn('snapshot_data');
            }
            if (Schema::hasColumn('orders', 'buyer_email')) {
                // Keep buyer_email for now as discussed
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_name')->nullable();
            $table->string('shipping_phone')->nullable();
            $table->string('shipping_address')->nullable();
            $table->json('snapshot_data')->nullable();
        });

        // Reverse data migration (optional, hard to do perfectly)
    }
};
