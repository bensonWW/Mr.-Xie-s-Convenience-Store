<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart($request->user());
        $cart->load(['items.product', 'items.variant']);

        // Add computed fields to items
        $items = $cart->items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'product' => $item->product,
                'variant' => $item->variant,
                'effective_price' => $item->effective_price,
                'current_price' => $item->current_price,
                'snapshot_price' => $item->snapshot_price,
                'price_changed' => $item->hasPriceChanged(),
                'display_name' => $item->display_name,
                'effective_image' => $item->effective_image,
                'effective_stock' => $item->effective_stock,
                'line_total' => $item->line_total,
            ];
        });

        return response()->json([
            'id' => $cart->id,
            'user_id' => $cart->user_id,
            'items' => $items,
            'total' => $items->sum('line_total'),
        ]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // If product has variants, variant_id is required
        if ($product->has_variants && !$request->variant_id) {
            return response()->json(['message' => '請選擇商品規格'], 422);
        }

        // Validate variant belongs to product
        if ($request->variant_id) {
            $variant = ProductVariant::where('id', $request->variant_id)
                ->where('product_id', $request->product_id)
                ->where('is_active', true)
                ->first();

            if (!$variant) {
                return response()->json(['message' => '無效的商品規格'], 422);
            }

            // Check variant stock
            if ($variant->stock < $request->quantity) {
                return response()->json(['message' => '庫存不足'], 422);
            }
        } else {
            // Check product stock
            if ($product->stock < $request->quantity) {
                return response()->json(['message' => '庫存不足'], 422);
            }
        }

        $cart = $this->cartService->addItem(
            $request->user(),
            $request->product_id,
            $request->quantity,
            $request->variant_id
        );

        return response()->json($cart);
    }

    public function updateItem(Request $request, $itemId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = $this->cartService->updateItem(
            $request->user(),
            $itemId,
            $request->quantity
        );

        return response()->json($item);
    }

    public function removeItem(Request $request, $itemId): JsonResponse
    {
        $this->cartService->removeItem($request->user(), $itemId);
        return response()->json(['message' => 'Item removed']);
    }
}
