<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\OrderService;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    public function index(Request $request)
    {
        $query = $request->user()->orders()->with('items.product')->latest();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return $query->get();
    }



    public function store(Request $request, \App\Services\WalletService $walletService, \App\Services\MemberLevelService $memberService)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        try {
            $transactionResult = DB::transaction(function () use ($user, $cart, $request, $walletService, $memberService) {
                $totalAmount = 0;
                $orderItemsData = [];

                // 1. Process items & Lock Stock
                foreach ($cart->items as $item) {
                    $product = \App\Models\Product::where('id', $item->product_id)->lockForUpdate()->first();

                    if (!$product) throw new \Exception("Product {$item->product_id} not found.");
                    if ($product->stock < $item->quantity) throw new \Exception("{$product->name} 庫存不足");

                    // Decrement Stock
                    $product->decrement('stock', $item->quantity);

                    $totalAmount += $product->price * $item->quantity;
                    $orderItemsData[] = [
                        'product_id' => $product->id,
                        'quantity' => $item->quantity,
                        'price' => (int) round($product->price),
                    ];
                }

                // 2. Calculate Member Discount (Applied to Subtotal)
                $memberDiscount = $memberService->calculateDiscount($user, $totalAmount);
                $totalAmount -= $memberDiscount;

                // 3. Handle Coupon (Applied after Member Discount? Or before? Usually stacking or separate?)
                // Let's assume Coupon applies to the remaining amount.
                if ($request->has('coupon_code')) {
                    $coupon = \App\Models\Coupon::where('code', $request->coupon_code)->first();
                    if ($coupon && $coupon->isValidFor($totalAmount)) {
                        $couponDiscount = $coupon->calculateDiscount($totalAmount);
                        $totalAmount -= $couponDiscount;
                    }
                }

                // 4. Calculate Shipping Fee
                $freeShippingThreshold = \App\Models\Setting::get('free_shipping_threshold', 1000); // Default 1000
                $shippingFeeConfig = \App\Models\Setting::get('shipping_fee', 60); // Default 60
                $shippingFee = 0;

                // Check against amount AFTER discounts (Net Spend)
                if ($totalAmount < $freeShippingThreshold) {
                    $shippingFee = $shippingFeeConfig;
                }

                $finalAmount = (int) round(max(0, $totalAmount + $shippingFee));

                // 4. Create Order First (to get ID)
                $order = Order::create([
                    'user_id' => $user->id,
                    'status' => 'processing', // Default to Processing (Paid) explicitly
                    'total_amount' => $finalAmount,
                    'discount_amount' => $memberDiscount, // Track member discount specifically
                    'shipping_fee' => $shippingFee,
                    'snapshot_data' => [
                        'customer_name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'shipping_address' => $user->address,
                        'member_level' => $user->member_level ?? 'normal', // Snapshot current level
                    ],
                ]);

                // 5. Create Order Items
                foreach ($orderItemsData as $data) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $data['product_id'],
                        'quantity' => $data['quantity'],
                        'price' => $data['price'],
                    ]);
                }

                // 6. Deduct Wallet Balance (Atomic)
                // If this fails, the whole transaction rolls back (Order is deleted, Stock restored)
                $walletService->pay(
                    $user,
                    $finalAmount,
                    "Payment for Order #{$order->id}",
                    "ORDER_{$order->id}"
                );

                // 7. Clear Cart
                $cart->items()->delete();

                return $order->load('items.product');
            });

            // Fire event after transaction commits successfully
            \App\Events\OrderPaid::dispatch($transactionResult);

            return response()->json($transactionResult, 201);
        } catch (\Exception $e) {
            // Check if it's a balance issue
            if (str_contains($e->getMessage(), 'Insufficient balance')) {
                return response()->json(['message' => '餘額不足，請先儲值', 'error_code' => 'INSUFFICIENT_BALANCE'], 402);
            }
            return response()->json(['message' => $e->getMessage()], 400);
        }
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
    public function refund(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($request->user()->id !== $order->user_id && !$request->user()->isAdmin()) { // Assuming isAdmin helper exists or use gate
            // For now, simplify: Only checking ownership. 
            // If Admin implementation is separate, this might need Guard.
            // Let's assume User can cancel their own 'processing' order?
            // Or only Admin? 
            // PRD: "Full Refund (Cancelled)".
            // User usually "Cancels". Admin "Refunds".
            // Let's allow User to cancel if it matches their ID.
            if ($request->user()->id !== $order->user_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        try {
            $refundedOrder = $this->orderService->refund($order);
            return response()->json(['message' => 'Order refunded successfully', 'order' => $refundedOrder]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
