<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    /** @use HasFactory<\Database\Factories\CouponFactory> */
    use HasFactory;

    protected $table = 'coupons';
    protected $primaryKey = 'coupon_id';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'discount',
        'limit_price',
        'starts_at',
        'ends_at',
        'type',
    ];

    public function usedCoupons()
    {
        return $this->hasMany(UsedCoupon::class, 'coupon_id', 'coupon_id');
    }
}
