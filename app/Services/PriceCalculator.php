<?php

namespace App\Services;

use App\Models\User;
use App\Models\Coupon;
use App\Services\DTO\PriceResult;
use Illuminate\Support\Collection;

class PriceCalculator
{
    public function __construct(
        protected MemberLevelService $memberService,
        protected SystemConfigService $configService
    ) {}

    /**
     * Calculate full pricing details.
     * 
     * @param User $user
     * @param Collection $items Collection of OrderItems (or CartItems with product relation)
     * @param Coupon|null $coupon
     * @return PriceResult
     */
    public function calculate(User $user, Collection $items, ?Coupon $coupon = null): PriceResult
    {
        $subtotal = 0;
        $itemDetails = [];

        // 1. Calculate Subtotal
        foreach ($items as $item) {
            // Support both CartItem (with product relation) and manual item arrays
            // Using round() as safeguard for any unexpected float values
            $price = $item->product ? (int) round($item->product->price) : (int) round($item->price);
            $quantity = $item->quantity;

            $subtotal += $price * $quantity;

            $itemDetails[] = [
                'product_id' => $item->product_id,
                'price' => $price,
                'quantity' => $quantity,
                'subtotal' => $price * $quantity
            ];
        }

        // 2. Member Discount
        $memberDiscount = $this->memberService->calculateDiscount($user, $subtotal);
        $discountedSubtotal = max(0, $subtotal - $memberDiscount);

        // 3. Coupon Discount
        $couponDiscount = 0;
        if ($coupon && $coupon->isValidFor($discountedSubtotal)) {
            // Handle Coupon locally or delegate? Coupon model logic might return float.
            // Let's rely on Coupon logic but cast to int.
            // Note: Coupon::calculateDiscount logic in model needs to be checked if it relies on float math.
            // Since we know existing model exists, we use it but ensure integer return.
            $couponDiscount = (int) round($coupon->calculateDiscount($discountedSubtotal));
        }

        // Ensure we don't discount more than possible
        $couponDiscount = min($couponDiscount, $discountedSubtotal);
        $discountedSubtotal = max(0, $discountedSubtotal - $couponDiscount);

        // 4. Shipping Fee
        $freeShippingThreshold = $this->configService->getFreeShippingThreshold();
        $baseShippingFee = $this->configService->getShippingFee();

        $shippingFee = ($discountedSubtotal >= $freeShippingThreshold) ? 0 : $baseShippingFee;

        // 5. Final
        $finalAmount = $discountedSubtotal + $shippingFee;

        return new PriceResult(
            subtotal: $subtotal,
            memberDiscount: $memberDiscount,
            couponDiscount: $couponDiscount,
            shippingFee: $shippingFee,
            finalAmount: $finalAmount,
            itemDetails: $itemDetails
        );
    }
}
