<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
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
     * @throws ValidationException
     */
    public function addItem(User $user, int $productId, int $quantity): Cart
    {
        return DB::transaction(function () use ($user, $productId, $quantity) {
            $cart = $this->getOrCreateCart($user);

            // Lock product to check stock accurately
            $product = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

            $item = $cart->items()->where('product_id', $productId)->first();
            $currentQty = $item ? $item->quantity : 0;
            $newQty = $currentQty + $quantity;

            $this->validateStock($product, $newQty);

            if ($item) {
                $item->quantity = $newQty;
                $item->save();
            } else {
                $cart->items()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }

            return $cart->load('items.product');
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

            // Lock product for check
            $product = Product::where('id', $item->product_id)->lockForUpdate()->firstOrFail();

            $this->validateStock($product, $quantity);

            $item->quantity = $quantity;
            $item->save();

            return $item;
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
     * Validate that requested quantity doesn't exceed stock.
     * 
     * @throws ValidationException
     */
    private function validateStock(Product $product, int $requestedQuantity): void
    {
        if ($product->stock < $requestedQuantity) {
            throw ValidationException::withMessages([
                'quantity' => ["Insufficient stock. Only {$product->stock} remaining."],
            ]);
        }
    }
}
