<?php

namespace App\Exceptions;

use Exception;

/**
 * Thrown when an order is not in a payable state.
 */
class OrderNotPayableException extends Exception
{
    protected $message = 'Order is not pending payment';

    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? $this->message);
    }

    /**
     * Report the exception.
     */
    public function report(): bool
    {
        // Don't report this exception (it's a business logic exception)
        return false;
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render()
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error' => 'order_not_payable',
        ], 422);
    }
}
