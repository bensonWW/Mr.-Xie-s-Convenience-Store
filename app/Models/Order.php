<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $status
 * @property float $total_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderItem[] $items
 * @mixin \Illuminate\Database\Eloquent\Builder
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
        'snapshot_data',
    ];

    protected $casts = [
        'snapshot_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->logistics_number)) {
                $date = now()->format('Ymd');
                $unique = strtoupper(substr(uniqid(), -5));
                $order->logistics_number = "LOGI-{$date}-{$unique}";
            }
            if (empty($order->payment_method)) {
                $order->payment_method = 'wallet';
            }
        });
    }
}
