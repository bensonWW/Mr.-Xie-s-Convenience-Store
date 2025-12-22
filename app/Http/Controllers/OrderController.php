<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Services\OrderCreationService;
use App\Services\DTO\CreateOrderData;
use App\Enums\OrderStatus;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(Request $request)
    {
        $query = $request->user()->orders()->with('items.product')->latest();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return $query->get();
    }

    public function store(\App\Http\Requests\StoreOrderRequest $request, OrderCreationService $creationService)
    {
        try {
            // Build DTO
            $data = new CreateOrderData(
                user: $request->user(),
                couponCode: $request->coupon_code,
                shippingName: $request->input('shipping_name'),
                shippingPhone: $request->input('shipping_phone'),
                shippingAddress: $request->input('shipping_address')
            );

            $order = $creationService->execute($data);

            return response()->json($order, 201);
        } catch (\App\Exceptions\InsufficientBalanceException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function show(Request $request, Order $order)
    {
        $this->authorize('view', $order);
        return $order->load(['items.product', 'address', 'snapshot']);
    }

    public function pay(Request $request, $id)
    {
        $order = $request->user()->orders()->findOrFail($id);

        try {
            $paidOrder = $this->orderService->processPayment($order, $request->user());
            return response()->json(['message' => 'Payment successful', 'order' => $paidOrder]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateStatus(\App\Http\Requests\UpdateOrderStatusRequest $request, Order $order)
    {
        // Validation handled by FormRequest

        $newStatus = OrderStatus::from($request->status);

        if (!$this->orderService->canTransition($order, $newStatus)) {
            return response()->json([
                'message' => "Invalid status transition from {$order->status->value} to {$request->status}"
            ], 400);
        }

        $order->update(['status' => $newStatus]);

        return response()->json($order);
    }

    public function updateLogistics(Request $request, $id)
    {
        $request->validate([
            'logistics_number' => 'nullable|string|max:255'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['logistics_number' => $request->logistics_number]);

        return response()->json($order);
    }

    public function refund(Request $request, Order $order)
    {
        $this->authorize('refund', $order);

        try {
            $refundedOrder = $this->orderService->refund($order);
            return response()->json(['message' => 'Order refunded successfully', 'order' => $refundedOrder]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
