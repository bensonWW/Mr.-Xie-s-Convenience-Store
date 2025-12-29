<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

/**
 * Exception thrown when attempting to refund an order that is already refunded/cancelled.
 */
class OrderAlreadyRefundedException extends Exception
{
    public function __construct(string $message = 'Order is already refunded.')
    {
        parent::__construct($message);
    }

    /**
     * Report the exception.
     */
    public function report(): bool
    {
        // Don't log user errors
        return false;
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage()
            ], 400);
        }

        return response($this->getMessage(), 400);
    }
}
