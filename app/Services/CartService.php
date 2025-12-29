<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    /**
     * Get or create a cart for the user.
     */
    public function getOrCreateCart(User $user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    /**
     * Add an item to the user's cart.
     * 
     * @param User $user
     * @param int $productId
     * @param int $quantity
     * @param int|null $variantId
     * @return Cart
     * @throws ValidationException
     */
    public function addItem(User $user, int $productId, int $quantity, ?int $variantId = null): Cart
    {
        return DB::transaction(function () use ($user, $productId, $quantity, $variantId) {
            $cart = $this->getOrCreateCart($user);

            // Lock product to check stock accurately
            $product = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

            // Lock variant if specified
            $variant = null;
            $snapshotPrice = $product->price;

            if ($variantId) {
                $variant = ProductVariant::where('id', $variantId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->firstOrFail();
                $snapshotPrice = $variant->price;
            }

            // Check for existing item with same product AND variant
            $item = $cart->items()
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            $currentQty = $item ? $item->quantity : 0;
            $newQty = $currentQty + $quantity;

            // Validate stock
            if ($variant) {
                $this->validateVariantStock($variant, $newQty);
            } else {
                $this->validateStock($product, $newQty);
            }

            if ($item) {
                $item->quantity = $newQty;
                // Keep original snapshot price (don't update on quantity change)
                $item->save();
            } else {
                $cart->items()->create([
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                    'snapshot_price' => $snapshotPrice, // Lock price at add-time
                ]);
            }

            return $cart->load(['items.product', 'items.variant']);
        });
    }

    /**
     * Update an item quantity in the cart.
     * 
     * @throws ValidationException
     */
    public function updateItem(User $user, int $itemId, int $quantity): CartItem
    {
        return DB::transaction(function () use ($user, $itemId, $quantity) {
            $cart = Cart::where('user_id', $user->id)->firstOrFail();
            $item = $cart->items()->where('id', $itemId)->firstOrFail();

            // Lock product/variant for check
            if ($item->variant_id) {
                $variant = ProductVariant::where('id', $item->variant_id)->lockForUpdate()->firstOrFail();
                $this->validateVariantStock($variant, $quantity);
            } else {
                $product = Product::where('id', $item->product_id)->lockForUpdate()->firstOrFail();
                $this->validateStock($product, $quantity);
            }

            $item->quantity = $quantity;
            $item->save();

            return $item->load(['product', 'variant']);
        });
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(User $user, int $itemId): void
    {
        $cart = Cart::where('user_id', $user->id)->firstOrFail();
        $item = $cart->items()->where('id', $itemId)->firstOrFail();
        $item->delete();
    }

    /**
     * Clear all items from cart.
     */
    public function clearCart(User $user): void
    {
        $cart = Cart::where('user_id', $user->id)->first();
        if ($cart) {
            $cart->items()->delete();
        }
    }

    /**
     * Refresh snapshot prices to current prices.
     * Use this on checkout page to update prices and warn user.
     */
    public function refreshPrices(User $user): array
    {
        $cart = $this->getOrCreateCart($user);
        $cart->load(['items.product', 'items.variant']);

        $changes = [];

        foreach ($cart->items as $item) {
            $currentPrice = $item->current_price;
            $snapshotPrice = $item->snapshot_price;

            if ($snapshotPrice !== null && $snapshotPrice !== $currentPrice) {
                $changes[] = [
                    'item_id' => $item->id,
                    'product_name' => $item->display_name,
                    'old_price' => $snapshotPrice,
                    'new_price' => $currentPrice,
                ];

                // Update to current price
                $item->update(['snapshot_price' => $currentPrice]);
            }
        }

        return $changes;
    }

    /**
     * Validate that requested quantity doesn't exceed product stock.
     * 
     * @throws ValidationException
     */
    private function validateStock(Product $product, int $requestedQuantity): void
    {
        if ($product->stock < $requestedQuantity) {
            throw ValidationException::withMessages([
                'quantity' => ["庫存不足，僅剩 {$product->stock} 件"],
            ]);
        }
    }

    /**
     * Validate that requested quantity doesn't exceed variant stock.
     * 
     * @throws ValidationException
     */
    private function validateVariantStock(ProductVariant $variant, int $requestedQuantity): void
    {
        if ($variant->stock < $requestedQuantity) {
            throw ValidationException::withMessages([
                'quantity' => ["規格「{$variant->options_text}」庫存不足，僅剩 {$variant->stock} 件"],
            ]);
        }
    }
}
