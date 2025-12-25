<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * WalletTransaction model.
 * 
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property int $amount
 * @property int|null $order_id
 * @property int|null $refund_id
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Order|null $order
 */
class WalletTransaction extends Model
{
    use HasFactory;

    /**
     * Valid transaction types
     */
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_WITHDRAWAL = 'withdrawal';
    public const TYPE_PAYMENT = 'payment';
    public const TYPE_REFUND = 'refund';
    public const TYPE_ADJUSTMENT = 'adjustment';

    public const VALID_TYPES = [
        self::TYPE_DEPOSIT,
        self::TYPE_WITHDRAWAL,
        self::TYPE_PAYMENT,
        self::TYPE_REFUND,
        self::TYPE_ADJUSTMENT,
    ];

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'order_id',
        'refund_id',
        // 'reference_id', // REMOVED: Was ambiguous, now using explicit order_id/refund_id
        'description',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with this transaction.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
