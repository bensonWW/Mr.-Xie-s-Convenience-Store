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
        Schema::table('favorites', function (Blueprint $table) {
            $table->integer('price_at_add')->nullable()->after('product_id');
            $table->boolean('notify_price_drop')->default(true)->after('price_at_add');
            $table->boolean('notify_back_in_stock')->default(true)->after('notify_price_drop');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropColumn(['price_at_add', 'notify_price_drop', 'notify_back_in_stock']);
        });
    }
};
