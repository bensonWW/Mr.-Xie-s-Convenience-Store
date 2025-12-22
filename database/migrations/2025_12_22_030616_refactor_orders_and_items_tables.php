<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('buyer_email')->nullable()->after('user_id');
            // We'll rename it or drop it. Let's make it nullable first to be safe, then drop it if needed.
            // Plan said DROP. Let's DROP it to show commitment to the roast.
            $table->dropColumn('snapshot_data');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('product_id');
            $table->json('options')->nullable()->after('product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'options']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->json('snapshot_data')->nullable(); // Restore
            $table->dropColumn('buyer_email');
        });
    }
};
