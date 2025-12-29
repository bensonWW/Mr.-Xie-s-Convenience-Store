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
        Schema::table('special_events', function (Blueprint $table) {
            $table->index(['starts_at', 'ends_at'], 'special_events_starts_at_ends_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('special_events', function (Blueprint $table) {
            $table->dropIndex('special_events_starts_at_ends_at_index');
        });
    }
};
