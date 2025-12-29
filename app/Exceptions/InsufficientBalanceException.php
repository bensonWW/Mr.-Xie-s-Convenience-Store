<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InsufficientBalanceException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        // Return false to stop logging this exception if you don't want to fill logs with user errors
        return false;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage() ?: 'Insufficient balance.',
                'error_code' => 'INSUFFICIENT_BALANCE'
            ], 400);
        }

        // Fallback for non-JSON requests (though this is an API)
        return response('Insufficient Balance', 400);
    }
}
