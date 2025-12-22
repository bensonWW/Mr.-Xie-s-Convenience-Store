<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property \App\Enums\OrderStatus $status
 * @property float $total_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderItem[] $items
 * @property-read \App\Models\OrderAddress|null $address
 * @property-read \App\Models\OrderSnapshot|null $snapshot
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'total_amount',
        'discount_amount',
        'shipping_fee',
        'payment_method',
        'logistics_number',
        // 'buyer_email', // Moved to Snapshot
        // 'shipping_name', // Moved to Address
        // 'shipping_phone', // Moved to Address
        // 'shipping_address', // Moved to Address
        // 'snapshot_member_level', // Moved to Snapshot
    ];

    protected $casts = [
        'status' => \App\Enums\OrderStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->hasOne(OrderAddress::class);
    }

    public function snapshot()
    {
        return $this->hasOne(OrderSnapshot::class);
    }
}
