<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index()
    {
        return Order::with('user')->latest()->paginate(20);
    }

    public function show($id)
    {
        return Order::with('items.product', 'user', 'address', 'snapshot')->findOrFail($id);
    }

    /**
     * Refund an order (admin operation).
     */
    public function refund(Request $request, Order $order)
    {
        $this->authorize('refund', $order);
        $refundedOrder = $this->orderService->cancelAndRefund($order);
        return response()->json(['message' => 'Order refunded successfully', 'order' => $refundedOrder]);
    }

    /**
     * Update order status.
     */
    public function updateStatus(\App\Http\Requests\UpdateOrderStatusRequest $request, Order $order)
    {
        $newStatus = \App\Enums\OrderStatus::from($request->status);

        // Use transitionStatus which handles auto-refund and inventory restoration
        $updatedOrder = $this->orderService->transitionStatus($order, $newStatus);

        return response()->json($updatedOrder);
    }

    /**
     * Update order logistics number.
     */
    public function updateLogistics(Request $request, Order $order)
    {
        $request->validate([
            'logistics_number' => 'nullable|string|max:255'
        ]);

        $order->update(['logistics_number' => $request->logistics_number]);
        return response()->json($order);
    }
}
