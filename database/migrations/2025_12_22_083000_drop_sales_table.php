<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration drops the redundant 'sales' table.
     * Sales data can be derived from order_items via aggregation.
     */
    public function up(): void
    {
        Schema::dropIfExists('sales');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the sales table if rolling back
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->timestamp('sold_at');
            $table->timestamps();
        });
    }
};
