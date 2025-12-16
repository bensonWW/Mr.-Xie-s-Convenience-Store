<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->orders()->with('items.product')->latest()->get();
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        return DB::transaction(function () use ($user, $cart, $request) {
            $totalAmount = 0;
            $orderItems = [];

            // 1. Process items, validate stock, and decrement with lock
            foreach ($cart->items as $item) {
                // Lock the product row for update to prevent race conditions
                $product = \App\Models\Product::where('id', $item->product_id)->lockForUpdate()->first();

                if (!$product) {
                    throw new \Exception("Product {$item->product_id} not found.");
                }

                if ($product->stock < $item->quantity) {
                    throw new \Exception("Insufficient stock for {$product->name}.");
                }

                $product->decrement('stock', $item->quantity);

                $totalAmount += $product->price * $item->quantity;
                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'price' => (int) round($product->price), // Snapshot price as integer
                ];
            }

            // 2. Handle Coupon
            if ($request->has('coupon_code')) {
                $coupon = \App\Models\Coupon::where('code', $request->coupon_code)->first();
                if ($coupon && $coupon->isValidFor($totalAmount)) {
                    $discount = $coupon->calculateDiscount($totalAmount);
                    $totalAmount -= $discount;
                }
            }

            // 3. Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending_payment',
                'total_amount' => (int) round(max(0, $totalAmount)), // Integer currency
            ]);

            // 4. Create Order Items
            foreach ($orderItems as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                ]);
            }

            // Clear cart
            $cart->items()->delete();

            return response()->json($order->load('items.product'), 201);
        });
    }

    public function show(Request $request, $id)
    {
        return $request->user()->orders()->with('items.product')->findOrFail($id);
    }

    public function pay(Request $request, $id, \App\Services\WalletService $walletService)
    {
        $order = $request->user()->orders()->findOrFail($id);

        if ($order->status !== 'pending_payment') {
            return response()->json(['message' => 'Order is not pending payment'], 400);
        }

        try {
            // Deduct from wallet
            $walletService->withdraw(
                $request->user(),
                $order->total_amount,
                "Payment for Order #{$order->id}",
                "ORDER_{$order->id}"
            );

            $order->update(['status' => 'processing']);

            return response()->json(['message' => 'Payment successful', 'order' => $order]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending_payment,processing,shipped,completed,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return response()->json($order);
    }
}
