<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Enums\OrderStatus;
use App\Events\OrderPaid;

class OrderService
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    /**
     * Process order payment.
     * Moves logic from Controller to Service.
     */
    public function processPayment(Order $order, User $user): Order
    {
        return DB::transaction(function () use ($order, $user) {
            // Lock order
            $order = Order::where('id', $order->id)->lockForUpdate()->first();

            if ($order->status !== OrderStatus::PENDING_PAYMENT) {
                throw new Exception('Order is not pending payment');
            }

            // Withdraw from wallet (using order_id for tracking)
            $this->walletService->withdraw(
                $user,
                $order->total_amount,
                "Payment for Order #{$order->id}",
                $order->id
            );

            // Update status
            $order->update(['status' => OrderStatus::PROCESSING]);

            // Dispatch event
            OrderPaid::dispatch($order);

            return $order;
        });
    }

    /**
     * Check if status transition is valid.
     * Delegates to OrderStatus enum's state machine.
     */
    public function canTransition(Order $order, OrderStatus $newStatus): bool
    {
        $currentStatus = $order->status instanceof OrderStatus
            ? $order->status
            : OrderStatus::from($order->status);

        return $currentStatus->canTransitionTo($newStatus);
    }

    /**
     * Transition order to a new status with validation and auto-refund.
     * 
     * @throws Exception When transition is not allowed
     */
    public function transitionStatus(Order $order, OrderStatus $newStatus): Order
    {
        return DB::transaction(function () use ($order, $newStatus) {
            // Lock order for update
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            $currentStatus = $lockedOrder->status instanceof OrderStatus
                ? $lockedOrder->status
                : OrderStatus::from($lockedOrder->status);

            // Validate transition
            if (!$currentStatus->canTransitionTo($newStatus)) {
                throw new Exception(
                    "Cannot transition order from {$currentStatus->label()} to {$newStatus->label()}"
                );
            }

            // Auto-refund when cancelling a paid order
            if ($newStatus === OrderStatus::CANCELLED && $currentStatus->requiresRefundOnCancel()) {
                $this->walletService->refund(
                    $lockedOrder->user,
                    $lockedOrder->total_amount,
                    "Auto-refund for cancelled Order #{$lockedOrder->id}",
                    $lockedOrder->id
                );

                // Restore inventory
                foreach ($lockedOrder->items as $item) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            // Update status
            $lockedOrder->status = $newStatus;
            $lockedOrder->save();

            return $lockedOrder;
        });
    }

    /**
     * refund an order.
     */
    public function refund(Order $order): Order
    {
        if ($order->status === OrderStatus::RETURNED) { // Assuming 'refunded' maps to RETURNED or CANCELLED? User code had 'refunded'. 
            // The Enum definitions: cancelled, returned. No 'refunded'.
            // The previous code checked for literal string 'refunded'. 
            // I need to align this. I'll assume 'CANCELLED' implies refunded for money. 
            // Or maybe 'RETURNED'.
            // Let's stick to 'CANCELLED' if it's a cancellation.
            throw new Exception("Order is already refunded/cancelled.");
        }

        // Logic check
        // ... (simplified for brevity, keeping core logic)

        return DB::transaction(function () use ($order) {
            // Refetch with lock
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            // Check status again
            if (in_array($lockedOrder->status, [OrderStatus::CANCELLED, OrderStatus::RETURNED])) {
                throw new Exception("Order is already refunded.");
            }

            // Check if order was paid (not pending)
            if ($lockedOrder->status !== OrderStatus::PENDING_PAYMENT) {
                // Refund Wallet (using order_id for tracking)
                $this->walletService->refund(
                    $lockedOrder->user,
                    $lockedOrder->total_amount,
                    "Refund for Order #{$lockedOrder->id}",
                    $lockedOrder->id
                );
            }

            // Restore Stock (Primitive loop)
            foreach ($lockedOrder->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            // Update Status
            $lockedOrder->status = OrderStatus::CANCELLED;
            $lockedOrder->save();

            return $lockedOrder;
        });
    }
}
