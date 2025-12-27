<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Enums\OrderStatus;
use App\Events\OrderPaid;
use App\Exceptions\OrderAlreadyRefundedException;
use App\Exceptions\OrderNotPayableException;
use App\Exceptions\InvalidOrderTransitionException;

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
                throw new OrderNotPayableException();
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
     * @throws \App\Exceptions\InvalidOrderTransitionException When transition is not allowed
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
                throw new InvalidOrderTransitionException(
                    $currentStatus->value,
                    $newStatus->value
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

                // Restore inventory (eager load to avoid N+1)
                $lockedOrder->load('items.product');
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
     * Cancel an order and refund if already paid.
     * Sets order status to CANCELLED and restores inventory.
     */
    public function cancelAndRefund(Order $order): Order
    {
        // Quick check before entering transaction
        if ($order->status === OrderStatus::RETURNED || $order->status === OrderStatus::CANCELLED) {
            throw new OrderAlreadyRefundedException();
        }

        return DB::transaction(function () use ($order) {
            // Refetch with lock
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            // Check status again
            if (in_array($lockedOrder->status, [OrderStatus::CANCELLED, OrderStatus::RETURNED])) {
                throw new OrderAlreadyRefundedException();
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

            // Restore Stock (eager load to avoid N+1)
            $lockedOrder->load('items.product');
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
