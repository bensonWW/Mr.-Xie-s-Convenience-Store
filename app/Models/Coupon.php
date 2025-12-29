<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_amount',
        'limit_price',
        'type',
        'starts_at',
        'ends_at',
        'usage_limit',
        'usage_count'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
    ];

    public function isValidFor(int $totalAmount): bool
    {
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;
        if ($this->limit_price && $totalAmount < $this->limit_price) return false;
        // Check usage limit
        if ($this->usage_limit !== null && $this->usage_count >= $this->usage_limit) return false;
        return true;
    }

    public function calculateDiscount(int $totalAmount): int
    {
        $discount = 0;
        if ($this->type === 'fixed') {
            $discount = $this->discount_amount;
        } elseif ($this->type === 'percentage') {
            // Use bcmath for precision: (totalAmount * discount_amount) / 100
            $discount = (int) bcdiv(
                bcmul((string) $totalAmount, (string) $this->discount_amount, 0),
                '100',
                0
            );
        }
        return min($discount, $totalAmount);
    }
}
