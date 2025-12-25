<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'price_at_add',        // Price when added to favorites (for price drop detection)
        'notify_price_drop',   // Enable price drop notifications
        'notify_back_in_stock', // Enable back-in-stock notifications
    ];

    protected $casts = [
        'notify_price_drop' => 'boolean',
        'notify_back_in_stock' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if the product price has dropped since added to favorites.
     */
    public function hasPriceDropped(): bool
    {
        if (!$this->price_at_add || !$this->product) {
            return false;
        }
        return $this->product->price < $this->price_at_add;
    }

    /**
     * Get the price drop amount (in cents).
     */
    public function getPriceDropAmount(): int
    {
        if (!$this->hasPriceDropped()) {
            return 0;
        }
        return $this->price_at_add - $this->product->price;
    }

    /**
     * Scope: Get favorites that need price drop notification.
     */
    public function scopeNeedsPriceDropNotification($query)
    {
        return $query->where('notify_price_drop', true)
            ->whereHas('product', function ($q) {
                $q->whereRaw('products.price < favorites.price_at_add');
            });
    }

    /**
     * Scope: Get favorites that need back-in-stock notification.
     */
    public function scopeNeedsBackInStockNotification($query)
    {
        return $query->where('notify_back_in_stock', true)
            ->whereHas('product', function ($q) {
                $q->where('stock', '>', 0);
            });
    }
}
