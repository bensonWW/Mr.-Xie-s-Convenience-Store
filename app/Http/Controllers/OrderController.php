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
            $totalAmount = $cart->items->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            // Handle Coupon
            if ($request->has('coupon_code')) {
                $coupon = \App\Models\Coupon::where('code', $request->coupon_code)->first();
                if ($coupon) {
                    // Basic validation (should ideally reuse CouponController logic or a service)
                    $now = now();
                    $isValid = true;
                    if ($coupon->starts_at && $now->lt($coupon->starts_at)) $isValid = false;
                    if ($coupon->ends_at && $now->gt($coupon->ends_at)) $isValid = false;
                    if ($coupon->limit_price && $totalAmount < $coupon->limit_price) $isValid = false;

                    if ($isValid) {
                        $discount = 0;
                        if ($coupon->type === 'fixed') {
                            $discount = $coupon->discount_amount;
                        } elseif ($coupon->type === 'percentage') {
                            $discount = $totalAmount * ($coupon->discount_amount / 100);
                        }
                        $totalAmount -= min($discount, $totalAmount);
                    }
                }
            }

            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending_payment',
                'total_amount' => max(0, $totalAmount), // Ensure not negative
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
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

    public function pay(Request $request, $id)
    {
        $order = $request->user()->orders()->findOrFail($id);

        if ($order->status !== 'pending_payment') {
            return response()->json(['message' => 'Order is not pending payment'], 400);
        }

        $order->update(['status' => 'processing']);

        return response()->json(['message' => 'Payment successful', 'order' => $order]);
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
