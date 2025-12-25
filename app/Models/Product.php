<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $price
 * @property int|null $original_price
 * @property string|null $information
 * @property string|null $image
 * @property int|null $category_id
 * @property string $status
 * @property int $stock
 * @property int|null $store_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Store|null $store
 * @property-read \App\Models\Category|null $category
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'original_price',
        'information',
        'image',
        'category_id',
        'status',
        'stock',
        'store_id'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope to filter by category (using category_id or category name/slug).
     */
    public function scopeInCategory($query, $categoryIdentifier)
    {
        if (is_numeric($categoryIdentifier)) {
            return $query->where('category_id', $categoryIdentifier);
        }

        return $query->whereHas('category', function ($q) use ($categoryIdentifier) {
            $q->where('name', $categoryIdentifier)
                ->orWhere('slug', $categoryIdentifier);
        });
    }

    /**
     * Default low stock threshold.
     * Products with stock below this are considered low.
     */
    public const LOW_STOCK_THRESHOLD = 10;

    /**
     * Check if product has low stock.
     */
    public function isLowStock(?int $threshold = null): bool
    {
        $threshold = $threshold ?? self::LOW_STOCK_THRESHOLD;
        return $this->stock <= $threshold;
    }

    /**
     * Check if product is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Scope to get products with low stock.
     */
    public function scopeLowStock($query, ?int $threshold = null)
    {
        $threshold = $threshold ?? self::LOW_STOCK_THRESHOLD;
        return $query->where('stock', '<=', $threshold)->where('stock', '>', 0);
    }

    /**
     * Scope to get products that are out of stock.
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }
}
