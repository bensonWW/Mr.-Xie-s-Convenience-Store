<?php

namespace App\Services;

use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class InventoryService
{
    /**
     * Lock and check stock for a batch of products.
     * 
     * @param array $items Array of ['product_id' => int, 'quantity' => int]
     * @return LockedStock
     * @throws Exception
     */
    public function lockAndCheckStock(array $items): LockedStock
    {
        $productIds = array_column($items, 'product_id');

        if (empty($productIds)) {
            return new LockedStock(new Collection());
        }

        // Batch Lock: Pessimistic Concurrency Control (PCC)
        $products = Product::whereIn('id', $productIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($items as $item) {
            $productId = $item['product_id'];
            $quantity = $item['quantity'];

            if (!$products->has($productId)) {
                throw new Exception("Product ID {$productId} not found.");
            }

            $product = $products->get($productId);

            if ($product->stock < $quantity) {
                // Fail Fast
                throw new Exception("Product '{$product->name}' (ID: {$productId}) has insufficient stock. Required: {$quantity}, Available: {$product->stock}");
            }
        }

        return new LockedStock($products);
    }

    /**
     * Deduct stock for verified items.
     * 
     * @param LockedStock $lockedStock
     * @param array $items Array of ['product_id' => int, 'quantity' => int]
     * @return void
     */
    public function deductStock(LockedStock $lockedStock, array $items): void
    {
        $products = $lockedStock->getAll();

        foreach ($items as $item) {
            $product = $products->get($item['product_id']);
            $product->decrement('stock', $item['quantity']);
        }
    }
}
