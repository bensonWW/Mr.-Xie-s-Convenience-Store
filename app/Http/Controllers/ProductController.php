<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // If filtering, do NOT cache (too many variations). 
        // Only cache the default "landing" page 1 with no params.
        if (empty($request->all()) || (count($request->all()) == 1 && $request->has('page'))) {
            $page = $request->input('page', 1);
            // Cache key based on page
            return \Illuminate\Support\Facades\Cache::remember("products_active_page_{$page}", 300, function () {
                 return Product::where('status', 'active')->paginate(20);
            });
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return $query->where('status', 'active')->paginate(20);
    }

    public function adminIndex()
    {
        return Product::latest()->paginate(20);
    }

    public function show($id)
    {
        return Product::findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'required|string',
            'stock' => 'required|integer',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $data = $request->all();

        // Default store_id if not provided
        // Only allow if user belongs to a store, or explicit admin override
        if (!isset($data['store_id'])) {
            $user = $request->user();
            if ($user && $user->store_id) {
                $data['store_id'] = $user->store_id;
            } else {
                 // Fallback to first store only if explicitly allowed or single-store mode
                 // For safety, let's require store_id if user doesn't have one.
                 // But for compatibility with existing tests/seeders, we might keep a fallback but log a warning?
                 // No, let's follow the plan: Explicitly fail if setup is missing.
                 return response()->json(['message' => 'Store ID is required.'], 400);
            }
        }

        if ($request->hasFile('image')) {
             $path = $request->file('image')->store('images', 'public');
             $data['image'] = $path;
        }

        $product = Product::create($data);
        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'string',
            'price' => 'numeric',
            'category' => 'string',
            'stock' => 'integer',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image if exists
             if ($product->image && Storage::disk('public')->exists($product->image)) {
                 Storage::disk('public')->delete($product->image);
             }

            $path = $request->file('image')->store('images', 'public');
            $data['image'] = $path;
        }

        $product->update($data);
        return response()->json($product);
    }

    public function destroy($id)
    {
        Product::destroy($id);
        return response()->json(['message' => 'Product deleted']);
    }

    public function categories()
    {
        return Product::distinct()->pluck('category');
    }
}
