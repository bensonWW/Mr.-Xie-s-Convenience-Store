<?php

namespace App\Exceptions;

use Exception;

class EmptyCartException extends Exception
{
    public function __construct(string $message = 'Cart is empty')
    {
        parent::__construct($message);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render($request)
    {
        return response()->json(['message' => $this->getMessage()], 400);
    }
}
