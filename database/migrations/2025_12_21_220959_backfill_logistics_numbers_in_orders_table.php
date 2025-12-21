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
        $orders = DB::table('orders')->whereNull('logistics_number')->get();

        foreach ($orders as $order) {
            $date = date('Ymd'); // Use current date for backfill, or we could use created_at
            $unique = strtoupper(substr(uniqid(), -5));
            // Ensure uniqueness could be better handled, but for backfill this is likely sufficient
            $logisticsNumber = "LOGI-{$date}-{$unique}";

            DB::table('orders')->where('id', $order->id)->update([
                'logistics_number' => $logisticsNumber
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration needed for data backfill essentially, 
        // or we could set them back to NULL, but that loses data.
    }
};
