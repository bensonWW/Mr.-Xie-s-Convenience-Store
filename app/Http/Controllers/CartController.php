<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        return $cart->load('items.product');
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();

        $cart = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            // Lock product to check stock accurately
            $product = \App\Models\Product::where('id', $request->product_id)->lockForUpdate()->firstOrFail();

            $item = $cart->items()->where('product_id', $request->product_id)->first();
            $currentQty = $item ? $item->quantity : 0;
            $newQty = $currentQty + $request->quantity;

            if ($product->stock < $newQty) {
                // If insufficient, do not modify cart
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantity' => ["Insufficient stock. Only {$product->stock} remaining."],
                ]);
            }

            if ($item) {
                $item->quantity = $newQty;
                $item->save();
            } else {
                $item = $cart->items()->create([
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                ]);
            }

            return $cart->load('items.product');
        });

        return response()->json($cart);
    }

    public function updateItem(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $itemId) {
            $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();
            $item = $cart->items()->where('id', $itemId)->firstOrFail();

            // Lock product for check
            $product = \App\Models\Product::where('id', $item->product_id)->lockForUpdate()->firstOrFail();

            if ($product->stock < $request->quantity) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantity' => ["Insufficient stock. Only {$product->stock} remaining."],
                ]);
            }

            $item->quantity = $request->quantity;
            $item->save();

            return $item;
        });

        return response()->json($item);
    }

    public function removeItem(Request $request, $itemId)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $itemId) {
            $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();
            $item = $cart->items()->where('id', $itemId)->firstOrFail();

            $item->delete();

            return response()->json(['message' => 'Item removed']);
        });
    }
}
