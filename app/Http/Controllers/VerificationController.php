<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EmailVerificationService;

class VerificationController extends Controller
{
    /**
     * Send verification email to authenticated user.
     */
    public function send(Request $request, EmailVerificationService $service)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // 13.5: 60-second resend limit
        $key = 'verification-email:' . $user->id;

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            return response()->json(['message' => 'Please wait before resending', 'retry_after' => $seconds], 429);
        }

        try {
            $service->generateCode($user);
            $service->sendCode($user);

            \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

            return response()->json(['message' => 'Verification code sent']);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to send code', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify email by 4-char lowercase alphanumeric code.
     */
    public function verifyCode(Request $request, EmailVerificationService $service)
    {
        $request->validate([
            'code' => 'required|string|min:4|max:8'
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $ok = $service->verifyCode($user, $request->code);
        if (!$ok) {
            return response()->json(['message' => 'Invalid or expired code'], 400);
        }

        return response()->json(['message' => 'Email verified']);
    }
}
