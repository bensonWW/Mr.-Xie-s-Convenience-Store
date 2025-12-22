<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->json('data')->nullable(); // Stores member_level, buyer_email, prices, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_snapshots');
    }
};
