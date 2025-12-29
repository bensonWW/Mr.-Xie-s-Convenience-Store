<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
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

    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->orders()->with('items.product')->latest();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return response()->json($query->get());
    }

    public function store(\App\Http\Requests\StoreOrderRequest $request, OrderCreationService $creationService): JsonResponse
    {
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
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $this->authorize('view', $order);
        return response()->json($order->load(['items.product', 'address', 'snapshot']));
    }

    public function pay(Request $request, Order $order): JsonResponse
    {
        $this->authorize('pay', $order);
        $paidOrder = $this->orderService->processPayment($order, $request->user());
        return response()->json(['message' => 'Payment successful', 'order' => $paidOrder]);
    }

    public function updateStatus(\App\Http\Requests\UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        $newStatus = OrderStatus::from($request->status);

        // Use transitionStatus which handles auto-refund and inventory restoration
        $updatedOrder = $this->orderService->transitionStatus($order, $newStatus);

        return response()->json($updatedOrder);
    }

    public function updateLogistics(\App\Http\Requests\UpdateLogisticsRequest $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        $order->update(['logistics_number' => $request->logistics_number]);

        return response()->json($order);
    }

    public function refund(Request $request, Order $order): JsonResponse
    {
        $this->authorize('refund', $order);
        $refundedOrder = $this->orderService->cancelAndRefund($order);
        return response()->json(['message' => 'Order refunded successfully', 'order' => $refundedOrder]);
    }
}
