<?php

namespace App\Services\DTO;

class PriceResult
{
    public function __construct(
        public int $subtotal,
        public int $memberDiscount,
        public int $couponDiscount,
        public int $shippingFee,
        public int $finalAmount,
        public ?array $itemDetails = []
    ) {}

    public function toArray(): array
    {
        return [
            'subtotal'        => $this->subtotal,
            'member_discount' => $this->memberDiscount,
            'coupon_discount' => $this->couponDiscount,
            'shipping_fee'    => $this->shippingFee,
            'final_amount'    => $this->finalAmount,
        ];
    }
}
