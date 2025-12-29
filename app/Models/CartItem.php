<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'quantity',
        'snapshot_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'snapshot_price' => 'integer',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship to variant (optional).
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Get the effective price (snapshot or current).
     * If snapshot_price is set, use it (price at add-time).
     * Otherwise, get current price from variant or product.
     */
    public function getEffectivePriceAttribute(): int
    {
        if ($this->snapshot_price !== null) {
            return $this->snapshot_price;
        }

        if ($this->variant) {
            return $this->variant->price;
        }

        return $this->product->price;
    }

    /**
     * Get the current market price for comparison.
     */
    public function getCurrentPriceAttribute(): int
    {
        if ($this->variant) {
            return $this->variant->price;
        }
        return $this->product->price;
    }

    /**
     * Check if price has changed since adding to cart.
     */
    public function hasPriceChanged(): bool
    {
        if ($this->snapshot_price === null) {
            return false;
        }
        return $this->snapshot_price !== $this->current_price;
    }

    /**
     * Get the effective stock.
     */
    public function getEffectiveStockAttribute(): int
    {
        if ($this->variant) {
            return $this->variant->stock;
        }
        return $this->product->stock;
    }

    /**
     * Get display name including variant options.
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->product->name;
        if ($this->variant) {
            $name .= ' (' . $this->variant->options_text . ')';
        }
        return $name;
    }

    /**
     * Get the effective image.
     */
    public function getEffectiveImageAttribute(): ?string
    {
        if ($this->variant && $this->variant->image) {
            return $this->variant->image;
        }
        return $this->product->image;
    }

    /**
     * Calculate line total.
     */
    public function getLineTotalAttribute(): int
    {
        return $this->effective_price * $this->quantity;
    }
}
