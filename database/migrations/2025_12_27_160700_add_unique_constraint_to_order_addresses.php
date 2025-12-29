<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fix: Add UNIQUE constraint to order_addresses.order_id
     * An order should only have one shipping address.
     */
    public function up(): void
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            // Add unique constraint - one address per order
            $table->unique('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->dropUnique(['order_id']);
        });
    }
};
