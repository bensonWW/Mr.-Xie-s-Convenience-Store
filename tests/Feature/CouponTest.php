<?php

namespace Tests\Feature;

use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    public function test_fixed_coupon_logic_in_cents()
    {
        // $10.00 Off -> Stored as 1000 cents
        $coupon = Coupon::create([
            'code' => 'FIXED10',
            'type' => 'fixed',
            'discount_amount' => 1000,
            'limit_price' => 2000 // Min spend $20.00 -> 2000 cents
        ]);

        // Scenario 1: Total 1500 (Below Limit)
        $this->assertFalse($coupon->isValidFor(1500));

        // Scenario 2: Total 2500 (Above Limit)
        $this->assertTrue($coupon->isValidFor(2500));

        // Scenario 3: Calculate Discount
        // fixed discount = 1000
        $this->assertEquals(1000, $coupon->calculateDiscount(2500));

        // Scenario 4: Discount > Total
        $this->assertEquals(500, $coupon->calculateDiscount(500));
    }

    public function test_percentage_coupon_logic()
    {
        // 10% Off -> Stored as 10 (integer)
        $coupon = Coupon::create([
            'code' => 'PERCENT10',
            'type' => 'percentage',
            'discount_amount' => 10,
            'limit_price' => 0
        ]);

        // Scenario 1: Calculate Discount
        // Total 10000 ($100) -> 10% = 1000 ($10)
        // Logic: 10000 * (10 / 100) = 1000
        $this->assertEquals(1000, $coupon->calculateDiscount(10000));
    }
}
