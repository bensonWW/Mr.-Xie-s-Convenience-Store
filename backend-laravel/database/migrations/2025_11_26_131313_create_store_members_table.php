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
        Schema::create('store_members', function (Blueprint $table) {
            $table->foreignId('store_id')->constrained('stores', 'store_id')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members', 'member_id')->onDelete('cascade');
            $table->primary(['store_id','member_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_members');
    }
};
