<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Note: statefulApi() removed for pure token-based cross-domain auth
        // If you need cookie-based SPA auth, add it back and configure SANCTUM_STATEFUL_DOMAINS
        $middleware->alias([
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
            'is_admin_only' => \App\Http\Middleware\IsAdminOnly::class,
            'refresh_token' => \App\Http\Middleware\RefreshTokenExpiration::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Render generic exceptions as 400 Bad Request for API requests
        // This maintains the expected API behavior for business logic errors
        $exceptions->render(function (\Exception $e, \Illuminate\Http\Request $request) {
            // Skip if exception has its own render method (like InsufficientBalanceException)
            if (method_exists($e, 'render')) {
                return null; // Let the exception handle itself
            }

            // For API requests, convert runtime/logic exceptions to 400 JSON responses
            if ($request->expectsJson() && !$e instanceof \Illuminate\Validation\ValidationException) {
                // Only for specific business logic exceptions (not framework exceptions)
                $isBusinessException = $e instanceof \RuntimeException
                    || $e instanceof \InvalidArgumentException
                    || $e instanceof \DomainException
                    || str_contains(get_class($e), 'App\\');

                if ($isBusinessException) {
                    return response()->json([
                        'message' => $e->getMessage()
                    ], 400);
                }
            }

            return null; // Let framework handle other exceptions
        });
    })->create();
