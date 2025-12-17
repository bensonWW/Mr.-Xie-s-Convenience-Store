<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    // Get user's favorites
    public function index(Request $request)
    {
        $user = $request->user();
        // Return products that are favorited by this user
        // We can join the favorites table
        $favorites = $user->favorites()->with('product')->get();

        // Transform to return pure products list with favorite meta if needed
        // Or just map to products
        return response()->json($favorites->map(function ($fav) {
            return $fav->product;
        }));
    }

    // Add to favorites (or toggle if you prefer, but strict add is better for add-buttons)
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = $request->user();
        $productId = $request->product_id;

        // Check availability logic if needed? No, just wishlist.

        // Use syncWithoutDetaching or direct create. handle duplicates logic.
        // Or firstOrCreate
        $user->favorites()->firstOrCreate([
            'product_id' => $productId
        ]);

        return response()->json(['message' => 'Added to favorites']);
    }

    // Remove from favorites
    public function destroy(Request $request, $productId)
    {
        $user = $request->user();
        $user->favorites()->where('product_id', $productId)->delete();

        return response()->json(['message' => 'Removed from favorites']);
    }
}
