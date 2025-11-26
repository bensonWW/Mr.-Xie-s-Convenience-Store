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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->foreignId('member_id')->constrained('members', 'member_id')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->date('arrived_at')->nullable();
            $table->tinyInteger('payment')->default(0);
            $table->integer('fee')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->boolean('is_completed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
