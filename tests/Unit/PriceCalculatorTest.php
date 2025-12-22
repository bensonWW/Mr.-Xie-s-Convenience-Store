<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PriceCalculator;
use App\Services\MemberLevelService;
use App\Services\SystemConfigService;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class PriceCalculatorTest extends TestCase
{
    use RefreshDatabase;

    protected $priceCalculator;
    protected $memberService;
    protected $configService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->memberService = Mockery::mock(MemberLevelService::class);
        $this->configService = Mockery::mock(SystemConfigService::class);

        $this->priceCalculator = new PriceCalculator(
            $this->memberService,
            $this->configService
        );
    }

    public function test_calculates_price_with_rounding()
    {
        $user = User::factory()->create();

        // Product with "float" price (simulated via accessor or just partial value if DB allowed)
        // But we are testing the Calculator's handling.
        // We simulate an item object.
        $product = new Product();
        $product->id = 1;
        $product->price = 10050; // 100.50 dollars (cents) - Integer

        // Mock Item
        $item = new CartItem();
        $item->product_id = 1;
        $item->quantity = 2;
        $item->setRelation('product', $product);

        $items = collect([$item]);

        // Mock Config
        $this->configService->shouldReceive('getFreeShippingThreshold')->andReturn(100000); // 1000.00
        $this->configService->shouldReceive('getShippingFee')->andReturn(6000); // 60.00

        // Mock Member Discount (return integer)
        $this->memberService->shouldReceive('calculateDiscount')->andReturn(0);

        $result = $this->priceCalculator->calculate($user, $items, null);

        // 10050 * 2 = 20100
        $this->assertEquals(20100, $result->subtotal);
    }

    public function test_calculates_price_with_float_input_safeguard()
    {
        // This tests the (int) round() fix
        $user = User::factory()->create();

        $product = new Product();
        $product->id = 1;
        // Simulate a float price coming from somewhere unexpected (e.g. 99.9999)
        // In PHP, direct property assignment doesn't cast if not defined in model casts?
        // Eloquent casts 'price' => 'integer' usually.
        // But let's force it.
        $product->price = 99.9;

        $item = new CartItem();
        $item->product_id = 1;
        $item->quantity = 1;
        $item->setRelation('product', $product);

        $items = collect([$item]);

        $this->configService->shouldReceive('getFreeShippingThreshold')->andReturn(100000);
        $this->configService->shouldReceive('getShippingFee')->andReturn(6000);
        $this->memberService->shouldReceive('calculateDiscount')->andReturn(0);

        $result = $this->priceCalculator->calculate($user, $items, null);

        // Before fix: (int) 99.9 = 99
        // After fix: (int) round(99.9) = 100
        $this->assertEquals(100, $result->subtotal);
    }
}
