<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * refund an order.
     *
     * @param Order $order
     * @return Order
     * @throws Exception
     */
    public function refund(Order $order): Order
    {
        if ($order->status === 'refunded') {
            throw new Exception("Order is already refunded.");
        }

        // Allowed statuses for "Cancellation/Refund"
        // Processing (Paid) -> Refundable
        // Shipped -> Usually needs return first, but for now we allow Full Refund if Admin/User triggers it? 
        // User Cancellation: usually only if 'processing' or 'pending'. 
        // Admin Refund: Any status except refunded.
        // Let's rely on Controller to gatecheck Auth/Role, but here we gatecheck Logic.

        if (!in_array($order->status, ['processing', 'shipped', 'delivered', 'completed'])) {
            // If pending_payment, just cancel, no refund. But this method is 'refund'.
            // If status is 'pending_payment', logic is different (no money moved).
            // Let's assume this method is STRICTLY for refunding money.
            // If order wasn't paid, shouldn't call this.
            if ($order->status === 'pending_payment') {
                // Nothing to refund. Just Cancel.
                $order->update(['status' => 'cancelled']);
                return $order;
            }
        }

        return DB::transaction(function () use ($order) {
            // Refetch with lock
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if ($lockedOrder->status === 'refunded') {
                throw new Exception("Order is already refunded.");
            }

            // 1. Credit Wallet
            $this->walletService->refund(
                $lockedOrder->user,
                $lockedOrder->total_amount,
                "Refund for Order #{$lockedOrder->id}",
                "ORDER_REFUND_{$lockedOrder->id}"
            );

            // 2. Restore Stock?
            // "Full Refund (Cancelled)" implies stock should return?
            // PRD didn't explicitly say "Restore Stock", but "Cancelled" usually means stock returns.
            // Let's implement stock restoration for completeness of "Full Refund".
            foreach ($lockedOrder->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            // 3. Update Status
            $lockedOrder->status = 'refunded';
            $lockedOrder->save();

            return $lockedOrder;
        });
    }
}
