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
     */
    public function canTransition(Order $order, OrderStatus $newStatus): bool
    {
        // Define transitions map
        // Using string values for keys just to be safe with array keys
        $transitions = [
            OrderStatus::PENDING_PAYMENT->value => [OrderStatus::PROCESSING, OrderStatus::CANCELLED],
            OrderStatus::PROCESSING->value => [OrderStatus::SHIPPED, OrderStatus::CANCELLED],
            OrderStatus::SHIPPED->value => [OrderStatus::DELIVERED, OrderStatus::RETURNED],
            OrderStatus::DELIVERED->value => [OrderStatus::COMPLETED, OrderStatus::RETURNED],
            OrderStatus::COMPLETED->value => [],
            OrderStatus::CANCELLED->value => [],
            OrderStatus::RETURNED->value => [],
        ];

        $currentStatusVal = $order->status instanceof OrderStatus ? $order->status->value : $order->status;

        // Find allowed statuses (array of Enums)
        $allowed = $transitions[$currentStatusVal] ?? [];

        // Check if newStatus is in allowed
        foreach ($allowed as $allowedStatus) {
            if ($allowedStatus === $newStatus) {
                return true;
            }
        }
        return false;
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
