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
