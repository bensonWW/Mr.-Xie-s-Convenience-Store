<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class LockedStock
{
    /**
     * @param Collection|Product[] $products Keyed by ID
     */
    public function __construct(
        protected Collection $products
    ) {}

    public function get(int $productId): Product
    {
        if (!$this->products->has($productId)) {
            throw new Exception("Product ID {$productId} is not locked or not found.");
        }
        return $this->products->get($productId);
    }

    public function getAll(): Collection
    {
        return $this->products;
    }
}
