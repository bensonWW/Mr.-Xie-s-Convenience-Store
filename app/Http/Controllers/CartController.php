<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(Request $request)
    {
        $cart = $this->cartService->getOrCreateCart($request->user());
        return $cart->load('items.product');
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->cartService->addItem(
            $request->user(),
            $request->product_id,
            $request->quantity
        );

        return response()->json($cart);
    }

    public function updateItem(Request $request, $itemId)
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

    public function removeItem(Request $request, $itemId)
    {
        $this->cartService->removeItem($request->user(), $itemId);
        return response()->json(['message' => 'Item removed']);
    }
}
