<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING_PAYMENT = 'pending_payment';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case RETURNED = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::PENDING_PAYMENT => 'Pending Payment',
            self::PROCESSING => 'Processing',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::RETURNED => 'Returned',
        };
    }

    /**
     * Get allowed transitions from current status.
     * 
     * State Machine:
     * pending_payment -> processing (payment received)
     * pending_payment -> cancelled (cancelled before payment)
     * processing -> shipped
     * processing -> cancelled (with refund)
     * shipped -> delivered (customer received)
     * shipped -> returned (customer refused)
     * delivered -> completed (confirmed)
     * delivered -> returned (return requested)
     * completed -> returned (post-purchase return)
     * 
     * @return array<OrderStatus>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PENDING_PAYMENT => [self::PROCESSING, self::CANCELLED],
            self::PROCESSING => [self::SHIPPED, self::CANCELLED],
            self::SHIPPED => [self::DELIVERED, self::COMPLETED, self::RETURNED], // Added COMPLETED for simplified flow
            self::DELIVERED => [self::COMPLETED, self::RETURNED],
            self::COMPLETED => [self::RETURNED],
            self::CANCELLED => [], // Terminal state
            self::RETURNED => [],  // Terminal state
        };
    }

    /**
     * Check if transition to target status is allowed.
     */
    public function canTransitionTo(OrderStatus $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    /**
     * Check if this status requires a refund when transitioning to CANCELLED.
     * Only PROCESSING orders (already paid) need refund.
     */
    public function requiresRefundOnCancel(): bool
    {
        return $this === self::PROCESSING;
    }

    /**
     * Check if this is a paid status.
     */
    public function isPaid(): bool
    {
        return in_array($this, [
            self::PROCESSING,
            self::SHIPPED,
            self::DELIVERED,
            self::COMPLETED,
        ], true);
    }

    /**
     * Check if this is a terminal/final state.
     * Note: COMPLETED is not terminal because it can still transition to RETURNED.
     */
    public function isTerminal(): bool
    {
        return in_array($this, [
            self::CANCELLED,
            self::RETURNED,
        ], true);
    }
}
