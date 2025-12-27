<?php

namespace App\Exceptions;

use Exception;

/**
 * Thrown when an order status transition is not allowed.
 */
class InvalidOrderTransitionException extends Exception
{
    protected string $fromStatus;
    protected string $toStatus;

    public function __construct(string $fromStatus, string $toStatus)
    {
        $this->fromStatus = $fromStatus;
        $this->toStatus = $toStatus;
        parent::__construct("Cannot transition order from {$fromStatus} to {$toStatus}");
    }

    public function getFromStatus(): string
    {
        return $this->fromStatus;
    }

    public function getToStatus(): string
    {
        return $this->toStatus;
    }

    /**
     * Report the exception.
     */
    public function report(): bool
    {
        return false;
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render()
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error' => 'invalid_order_transition',
            'from_status' => $this->fromStatus,
            'to_status' => $this->toStatus,
        ], 400);
    }
}
