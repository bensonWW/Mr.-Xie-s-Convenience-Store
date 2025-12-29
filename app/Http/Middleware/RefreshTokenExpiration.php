<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokenExpiration
{
    /**
     * Handle an incoming request.
     * Implements Sliding Expiration (ADR-007)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user && $user->currentAccessToken()) {
            $token = $user->currentAccessToken();

            if ($token instanceof PersonalAccessToken) {
                // 如果 Token 還有 24 小時內的有效期，則延長過期時間 (reset expiration)
                $expiration = config('sanctum.expiration');

                if ($expiration) {
                    $token->created_at = now();
                    $token->save();
                }
            }
        }

        return $next($request);
    }
}
