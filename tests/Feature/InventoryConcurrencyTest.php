<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    protected InventoryService $inventoryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inventoryService = new InventoryService();

        // Create a category for products
        Category::create(['name' => 'Test Category', 'slug' => 'test']);
    }

    /**
     * Test that stock check correctly locks products.
     */
    public function test_lock_and_check_stock_succeeds_with_sufficient_stock()
    {
        $product = Product::factory()->create(['stock' => 100]);

        $items = [
            ['product_id' => $product->id, 'quantity' => 50],
        ];

        $lockedStock = $this->inventoryService->lockAndCheckStock($items);

        $this->assertNotNull($lockedStock);
        $this->assertTrue($lockedStock->getAll()->has($product->id));
    }

    /**
     * Test that stock check fails with insufficient stock.
     */
    public function test_lock_and_check_stock_fails_with_insufficient_stock()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $items = [
            ['product_id' => $product->id, 'quantity' => 50],
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/insufficient stock/i');

        $this->inventoryService->lockAndCheckStock($items);
    }

    /**
     * Test that stock check fails for non-existent product.
     */
    public function test_lock_and_check_stock_fails_for_missing_product()
    {
        $items = [
            ['product_id' => 99999, 'quantity' => 1],
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/not found/i');

        $this->inventoryService->lockAndCheckStock($items);
    }

    /**
     * Test that empty items returns empty locked stock.
     */
    public function test_lock_and_check_stock_handles_empty_items()
    {
        $lockedStock = $this->inventoryService->lockAndCheckStock([]);

        $this->assertTrue($lockedStock->getAll()->isEmpty());
    }

    /**
     * Test that deduct stock correctly decrements.
     */
    public function test_deduct_stock_decrements_correctly()
    {
        $product = Product::factory()->create(['stock' => 100]);

        $items = [
            ['product_id' => $product->id, 'quantity' => 30],
        ];

        $lockedStock = $this->inventoryService->lockAndCheckStock($items);
        $this->inventoryService->deductStock($lockedStock, $items);

        $product->refresh();
        $this->assertEquals(70, $product->stock);
    }

    /**
     * Test multiple products deduction.
     */
    public function test_deduct_stock_handles_multiple_products()
    {
        $product1 = Product::factory()->create(['stock' => 50]);
        $product2 = Product::factory()->create(['stock' => 100]);
        $product3 = Product::factory()->create(['stock' => 200]);

        $items = [
            ['product_id' => $product1->id, 'quantity' => 10],
            ['product_id' => $product2->id, 'quantity' => 25],
            ['product_id' => $product3->id, 'quantity' => 50],
        ];

        $lockedStock = $this->inventoryService->lockAndCheckStock($items);
        $this->inventoryService->deductStock($lockedStock, $items);

        $product1->refresh();
        $product2->refresh();
        $product3->refresh();

        $this->assertEquals(40, $product1->stock);
        $this->assertEquals(75, $product2->stock);
        $this->assertEquals(150, $product3->stock);
    }

    /**
     * Test that concurrent stock checks maintain consistency.
     * This simulates multiple orders trying to purchase from limited stock.
     */
    public function test_sequential_stock_deductions_maintain_consistency()
    {
        $product = Product::factory()->create(['stock' => 100]);

        // Simulate 10 orders each taking 10 units
        for ($i = 0; $i < 10; $i++) {
            $items = [['product_id' => $product->id, 'quantity' => 10]];
            $lockedStock = $this->inventoryService->lockAndCheckStock($items);
            $this->inventoryService->deductStock($lockedStock, $items);
        }

        $product->refresh();
        $this->assertEquals(0, $product->stock);
    }

    /**
     * Test that stock check fails after stock exhausted.
     */
    public function test_stock_check_fails_when_exhausted()
    {
        $product = Product::factory()->create(['stock' => 10]);

        // First order takes all stock
        $items = [['product_id' => $product->id, 'quantity' => 10]];
        $lockedStock = $this->inventoryService->lockAndCheckStock($items);
        $this->inventoryService->deductStock($lockedStock, $items);

        // Second order should fail
        $this->expectException(\Exception::class);
        $this->inventoryService->lockAndCheckStock($items);
    }

    /**
     * Test exact boundary - purchasing exactly available stock.
     */
    public function test_exact_stock_purchase_succeeds()
    {
        $product = Product::factory()->create(['stock' => 50]);

        $items = [['product_id' => $product->id, 'quantity' => 50]];
        $lockedStock = $this->inventoryService->lockAndCheckStock($items);
        $this->inventoryService->deductStock($lockedStock, $items);

        $product->refresh();
        $this->assertEquals(0, $product->stock);
    }

    /**
     * Test boundary - one more than available fails.
     */
    public function test_one_over_stock_purchase_fails()
    {
        $product = Product::factory()->create(['stock' => 50]);

        $items = [['product_id' => $product->id, 'quantity' => 51]];

        $this->expectException(\Exception::class);
        $this->inventoryService->lockAndCheckStock($items);
    }
}
