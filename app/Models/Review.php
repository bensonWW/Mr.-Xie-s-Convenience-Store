<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property int|null $order_id
 * @property int $rating
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\Order|null $order
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Get the user who wrote the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product being reviewed.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the order associated with the review.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope to get reviews for a specific product.
     */
    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Boot method to update product rating cache when review is created/updated/deleted.
     */
    protected static function booted(): void
    {
        static::created(function (Review $review) {
            $review->updateProductRatingCache();
        });

        static::updated(function (Review $review) {
            $review->updateProductRatingCache();
        });

        static::deleted(function (Review $review) {
            $review->updateProductRatingCache();
        });
    }

    /**
     * Update the product's rating cache.
     */
    protected function updateProductRatingCache(): void
    {
        $product = $this->product;
        if ($product) {
            $product->rating_avg = $product->reviews()->avg('rating') ?? 0;
            $product->rating_count = $product->reviews()->count();
            $product->save();
        }
    }
}
