<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * OrderItemOption - Normalized options for order items.
 * 
 * @property int $id
 * @property int $order_item_id
 * @property string $option_name
 * @property string $option_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OrderItem $orderItem
 */
class OrderItemOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'option_name',
        'option_value',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
