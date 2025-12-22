<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * OrderSnapshot - Stores immutable snapshot data from order creation time.
 * 
 * @property int $id
 * @property int $order_id
 * @property string|null $buyer_email
 * @property string|null $member_level_name
 * @property string|null $user_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 */
class OrderSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'buyer_email',
        'member_level_name',
        'user_name',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
