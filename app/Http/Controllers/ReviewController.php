<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    /**
     * Get reviews for a product (public).
     */
    public function index(int $productId)
    {
        $product = Product::findOrFail($productId);

        $reviews = $product->reviews()
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($reviews);
    }

    /**
     * Store a new review (requires authentication and purchase verification).
     */
    public function store(Request $request, int $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($productId);

        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existingReview) {
            throw ValidationException::withMessages([
                'product' => '您已經評價過此商品',
            ]);
        }

        // Purchase verification: check if user has completed order with this product
        $completedOrder = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->first();

        if (!$completedOrder) {
            throw ValidationException::withMessages([
                'product' => '只有購買過此商品的用戶才能評價',
            ]);
        }

        // Create the review
        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $productId,
            'order_id' => $completedOrder->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $review->load('user:id,name');

        return response()->json([
            'message' => '評價已送出',
            'review' => $review,
        ], 201);
    }

    /**
     * Delete own review.
     */
    public function destroy(int $reviewId)
    {
        $user = Auth::user();
        $review = Review::findOrFail($reviewId);

        if ($review->user_id !== $user->id) {
            return response()->json(['error' => '無權刪除此評價'], 403);
        }

        $review->delete();

        return response()->json(['message' => '評價已刪除']);
    }

    /**
     * Check if current user can review a product.
     */
    public function canReview(int $productId)
    {
        $user = Auth::user();

        // Check if already reviewed
        $hasReviewed = Review::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();

        if ($hasReviewed) {
            return response()->json([
                'can_review' => false,
                'reason' => 'already_reviewed',
            ]);
        }

        // Check if has completed order
        $hasPurchased = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->exists();

        return response()->json([
            'can_review' => $hasPurchased,
            'reason' => $hasPurchased ? null : 'not_purchased',
        ]);
    }
}
