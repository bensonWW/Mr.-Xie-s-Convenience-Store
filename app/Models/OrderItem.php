<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * OrderItem model.
 * 
 * @property int $id
 * @property int $order_id
 * @property int|null $product_id
 * @property string|null $product_name
 * @property int $quantity
 * @property int $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product|null $product
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderItemOption[] $options
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        // 'options', // REMOVED: Now normalized to order_item_options table
        'quantity',
        'price'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the normalized options for this order item.
     */
    public function options()
    {
        return $this->hasMany(OrderItemOption::class);
    }

    /**
     * Helper to get options as key-value array (for backwards compatibility).
     */
    public function getOptionsArrayAttribute(): array
    {
        return $this->options->pluck('option_value', 'option_name')->toArray();
    }
}
