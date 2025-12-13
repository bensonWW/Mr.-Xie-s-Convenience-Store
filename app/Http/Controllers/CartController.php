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

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $product = \App\Models\Product::findOrFail($request->product_id);

        $item = $cart->items()->where('product_id', $request->product_id)->first();
        $currentQty = $item ? $item->quantity : 0;
        $newQty = $currentQty + $request->quantity;

        if ($product->stock < $newQty) {
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

        return response()->json($cart->load('items.product'));
    }

    public function updateItem(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();
        $item = $cart->items()->with('product')->where('id', $itemId)->firstOrFail();

        if ($item->product->stock < $request->quantity) {
             throw \Illuminate\Validation\ValidationException::withMessages([
                'quantity' => ["Insufficient stock. Only {$item->product->stock} remaining."],
            ]);
        }

        $item->quantity = $request->quantity;
        $item->save();

        return response()->json($item);
    }

    public function removeItem(Request $request, $itemId)
    {
        $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();
        $item = $cart->items()->where('id', $itemId)->firstOrFail();

        $item->delete();

        return response()->json(['message' => 'Item removed']);
    }
}
