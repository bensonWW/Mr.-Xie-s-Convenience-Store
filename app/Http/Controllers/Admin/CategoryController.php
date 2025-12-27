<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Get all categories with product counts.
     */
    public function index(): JsonResponse
    {
        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    /**
     * Create a new category.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        Cache::forget('categories');

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category,
        ], 201);
    }

    /**
     * Update a category.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($id)],
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        Cache::forget('categories');

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category->fresh(),
        ]);
    }

    /**
     * Delete a category.
     * Products in this category will have their category_id set to null.
     */
    public function destroy(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $productCount = Product::where('category_id', $id)->count();

        // Set products in this category to uncategorized (null)
        Product::where('category_id', $id)->update(['category_id' => null]);

        $category->delete();

        Cache::forget('categories');

        return response()->json([
            'message' => "Category deleted successfully. {$productCount} products are now uncategorized.",
            'affected_products' => $productCount,
        ]);
    }

    /**
     * Reassign products from one category to another, then delete the source category.
     */
    public function reassignAndDelete(Request $request, int $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'target_category_id' => 'required|integer|exists:categories,id|different:' . $id,
        ]);

        $productCount = Product::where('category_id', $id)->count();

        // Move products to target category
        Product::where('category_id', $id)->update(['category_id' => $validated['target_category_id']]);

        $targetCategory = Category::find($validated['target_category_id']);
        $category->delete();

        Cache::forget('categories');

        return response()->json([
            'message' => "Category deleted. {$productCount} products moved to '{$targetCategory->name}'.",
            'affected_products' => $productCount,
        ]);
    }
}
