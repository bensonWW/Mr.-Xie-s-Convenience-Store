<?php

namespace App\Exceptions;

use Exception;

/**
 * Thrown when a wallet operation is invalid.
 */
class InvalidWalletOperationException extends Exception
{
    public function __construct(string $message = 'Invalid wallet operation')
    {
        parent::__construct($message);
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
            'error' => 'invalid_wallet_operation',
        ], 422);
    }
}
