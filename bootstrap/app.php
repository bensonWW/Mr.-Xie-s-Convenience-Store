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
            'refresh_token' => \App\Http\Middleware\RefreshTokenExpiration::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
