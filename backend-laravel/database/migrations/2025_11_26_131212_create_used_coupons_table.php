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
        Schema::create('used_coupons', function (Blueprint $table) {
            $table->id('ucoupon_id');
            $table->foreignId('coupon_id')->constrained('coupons', 'coupon_id')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members', 'member_id')->onDelete('cascade');
            $table->timestamp('used_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('used_coupons');
    }
};
