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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->foreignId('store_id')->constrained('stores', 'store_id')->onDelete('cascade');
            $table->string('name');
            $table->integer('price')->default(0);
            $table->text('information')->nullable();
            $table->integer('stock')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->string('category')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
