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
     * This migration creates a categories table to normalize product categories (3NF).
     * It migrates existing category strings from products table.
     */
    public function up(): void
    {
        // 1. Create categories table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Check if products table has 'category' column (may not exist if fresh install)
        if (!Schema::hasColumn('products', 'category')) {
            // Fresh install: just add category_id
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            });
            return;
        }

        // 3. Migrate existing categories from products
        $existingCategories = DB::table('products')
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category');

        foreach ($existingCategories as $categoryName) {
            $slug = \Illuminate\Support\Str::slug($categoryName);

            // Handle duplicate slugs
            $baseSlug = $slug;
            $counter = 1;
            while (DB::table('categories')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            DB::table('categories')->insert([
                'name' => $categoryName,
                'slug' => $slug,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Add category_id column to products (nullable)
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
        });

        // 5. Update products with category_id
        $categories = DB::table('categories')->get()->keyBy('name');

        foreach (DB::table('products')->get() as $product) {
            if ($product->category && isset($categories[$product->category])) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['category_id' => $categories[$product->category]->id]);
            }
        }

        // 6. Drop old category column (skip for SQLite which doesn't support this)
        $driver = DB::connection()->getDriverName();
        if ($driver !== 'sqlite') {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
        // Note: For SQLite (testing), the old 'category' column remains but is unused.
        // The Product model now only uses category_id.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        // 1. Add back category column if it was dropped (not SQLite)
        if ($driver !== 'sqlite' && !Schema::hasColumn('products', 'category')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('category')->nullable()->after('image');
            });
        }

        // 2. Restore category names
        if (Schema::hasColumn('products', 'category')) {
            $categories = DB::table('categories')->get()->keyBy('id');

            foreach (DB::table('products')->get() as $product) {
                if ($product->category_id && isset($categories[$product->category_id])) {
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['category' => $categories[$product->category_id]->name]);
                }
            }
        }

        // 3. Drop category_id column
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
        });

        // 4. Drop categories table
        Schema::dropIfExists('categories');
    }
};
