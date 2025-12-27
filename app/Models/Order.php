<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\OrderStatus;

/**
 * @property int $id
 * @property int $user_id
 * @property \App\Enums\OrderStatus $status
 * @property int $total_amount Total in cents
 * @property int $discount_amount Discount in cents
 * @property int $shipping_fee Shipping fee in cents
 * @property int|null $coupon_id
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
        'coupon_id',
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

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Scope: Filter orders by user.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope: Get pending payment orders.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::PENDING_PAYMENT);
    }

    /**
     * Scope: Get paid orders (not pending, not cancelled, not returned).
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->whereIn('status', [
            OrderStatus::PROCESSING,
            OrderStatus::SHIPPED,
            OrderStatus::DELIVERED,
            OrderStatus::COMPLETED,
        ]);
    }

    /**
     * Scope: Get completed orders.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::COMPLETED);
    }
}
