<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsedCoupon extends Model
{
    /** @use HasFactory<\Database\Factories\UsedCouponFactory> */
    use HasFactory;

    protected $table = 'used_coupons';
    protected $primaryKey = 'ucoupon_id';
    public $timestamps = false;

    protected $fillable = [
        'coupon_id',
        'member_id',
        'used_at',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'coupon_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }
}
