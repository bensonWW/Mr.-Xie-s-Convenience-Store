<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // If filtering, do NOT cache (too many variations). 
        // Only cache the default "landing" page 1 with no params.
        if (empty($request->all()) || (count($request->all()) == 1 && $request->has('page'))) {
            $page = $request->input('page', 1);
            // Cache key based on page
            return Cache::remember("products_active_page_{$page}", 300, function () {
                return Product::with('category')->where('status', 'active')->paginate(20);
            });
        }

        // Filter by category (supports ID, slug, or name for backwards compatibility)
        if ($request->has('category')) {
            $categoryInput = $request->category;

            if (is_numeric($categoryInput)) {
                $query->where('category_id', $categoryInput);
            } else {
                // Support both slug and name for backwards compatibility
                $query->whereHas('category', function ($q) use ($categoryInput) {
                    $q->where('name', $categoryInput)
                        ->orWhere('slug', $categoryInput);
                });
            }
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return $query->where('status', 'active')->paginate(20);
    }

    public function adminIndex()
    {
        return Product::with('category')->latest()->paginate(20);
    }

    public function show($id)
    {
        return Product::with('category')->findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|integer|min:0',
            'original_price' => 'nullable|integer|min:0',
            'category' => 'required|string', // Can be category name or ID
            'stock' => 'required|integer',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $data = $request->only(['name', 'price', 'original_price', 'information', 'status', 'stock', 'store_id', 'category']);

        // Handle category: convert name/slug to category_id
        $categoryInput = $data['category'];
        unset($data['category']);

        if (is_numeric($categoryInput)) {
            $data['category_id'] = $categoryInput;
        } else {
            // Find or create category
            $category = Category::firstOrCreate(
                ['name' => $categoryInput],
                ['slug' => Str::slug($categoryInput)]
            );
            $data['category_id'] = $category->id;
        }

        // Resolve store_id: prefer explicit, fallback to user's store, or error
        if (!isset($data['store_id'])) {
            $user = $request->user();
            if ($user && $user->store_id) {
                $data['store_id'] = $user->store_id;
            } else {
                // Use first available store instead of creating ghost data
                $existingStore = \App\Models\Store::first();
                if (!$existingStore) {
                    abort(422, 'No store available. Please create a store first.');
                }
                $data['store_id'] = $existingStore->id;
            }
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $data['image'] = $path;
        }

        // Price is already in integer format from frontend, no conversion needed
        // Just ensure they are integers
        if (isset($data['price'])) {
            $data['price'] = (int) $data['price'];
        }
        if (isset($data['original_price'])) {
            $data['original_price'] = (int) $data['original_price'];
        }

        $product = Product::create($data);
        Cache::forget('categories');
        return response()->json($product->load('category'), 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'string',
            'price' => 'integer|min:0',
            'original_price' => 'nullable|integer|min:0',
            'category' => 'string', // Can be category name or ID
            'stock' => 'integer',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $data = $request->only(['name', 'price', 'original_price', 'information', 'status', 'stock', 'store_id', 'category']);

        // Handle category if provided
        if (isset($data['category'])) {
            $categoryInput = $data['category'];
            unset($data['category']);

            if (is_numeric($categoryInput)) {
                $data['category_id'] = $categoryInput;
            } else {
                // Find or create category
                $category = Category::firstOrCreate(
                    ['name' => $categoryInput],
                    ['slug' => Str::slug($categoryInput)]
                );
                $data['category_id'] = $category->id;
            }
        }

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $path = $request->file('image')->store('images', 'public');
            $data['image'] = $path;
        }

        // Price is already in integer format from frontend, no conversion needed
        if (isset($data['price'])) {
            $data['price'] = (int) $data['price'];
        }
        if (isset($data['original_price'])) {
            $data['original_price'] = (int) $data['original_price'];
        }

        $product->update($data);
        Cache::forget('categories');
        return response()->json($product->load('category'));
    }

    public function destroy($id)
    {
        Product::destroy($id);
        Cache::forget('categories');
        return response()->json(['message' => 'Product deleted']);
    }

    /**
     * Get all categories (normalized from categories table).
     */
    public function categories()
    {
        return Cache::remember('categories', 3600, function () {
            return Category::orderBy('name')->get(['id', 'name', 'slug']);
        });
    }
}
