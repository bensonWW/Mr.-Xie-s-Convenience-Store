<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration normalizes order_items.options JSON to a proper table (1NF).
     */
    public function up(): void
    {
        // 1. Create order_item_options table
        Schema::create('order_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->string('option_name');  // e.g., 'size', 'color', 'flavor'
            $table->string('option_value'); // e.g., 'large', 'red', 'vanilla'
            $table->timestamps();

            // Composite index for lookups
            $table->index(['order_item_id', 'option_name']);
        });

        // 2. Migrate existing JSON options data
        if (Schema::hasColumn('order_items', 'options')) {
            $items = DB::table('order_items')->whereNotNull('options')->get();

            foreach ($items as $item) {
                $options = json_decode($item->options, true);

                if (is_array($options)) {
                    foreach ($options as $name => $value) {
                        // Handle both key-value and simple array formats
                        if (is_string($name) && !is_numeric($name)) {
                            DB::table('order_item_options')->insert([
                                'order_item_id' => $item->id,
                                'option_name' => $name,
                                'option_value' => is_string($value) ? $value : json_encode($value),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            // 3. Drop old options column (skip for SQLite)
            $driver = DB::connection()->getDriverName();
            if ($driver !== 'sqlite') {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->dropColumn('options');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        // 1. Add back options column if it was dropped
        if ($driver !== 'sqlite' && !Schema::hasColumn('order_items', 'options')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->json('options')->nullable()->after('product_name');
            });
        }

        // 2. Migrate data back to JSON (best effort)
        if (Schema::hasColumn('order_items', 'options')) {
            $orderItemIds = DB::table('order_item_options')
                ->select('order_item_id')
                ->distinct()
                ->pluck('order_item_id');

            foreach ($orderItemIds as $itemId) {
                $options = DB::table('order_item_options')
                    ->where('order_item_id', $itemId)
                    ->get()
                    ->mapWithKeys(fn($opt) => [$opt->option_name => $opt->option_value])
                    ->toArray();

                DB::table('order_items')
                    ->where('id', $itemId)
                    ->update(['options' => json_encode($options)]);
            }
        }

        // 3. Drop order_item_options table
        Schema::dropIfExists('order_item_options');
    }
};
