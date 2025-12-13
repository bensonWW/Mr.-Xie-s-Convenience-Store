<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'discount_amount', 'limit_price', 'type', 'starts_at', 'ends_at'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function isValidFor($totalAmount)
    {
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;
        if ($this->limit_price && $totalAmount < $this->limit_price) return false;
        return true;
    }

    public function calculateDiscount($totalAmount)
    {
        $discount = 0;
        if ($this->type === 'fixed') {
            $discount = $this->discount_amount;
        } elseif ($this->type === 'percentage') {
            $discount = $totalAmount * ($this->discount_amount / 100);
        }
        return min($discount, $totalAmount);
    }
}
