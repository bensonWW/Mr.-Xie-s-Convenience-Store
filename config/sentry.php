<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sentry Laravel SDK Configuration - Error Monitoring Only
    |--------------------------------------------------------------------------
    |
    | This configuration is optimized for production error monitoring.
    | Only errors (not performance/traces) are sent to Sentry.
    |
    */

    'dsn' => env('SENTRY_LARAVEL_DSN', null),

    // Capture 100% of errors, but 0% of performance traces
    'traces_sample_rate' => 0.0,

    // Only send in production
    'send_default_pii' => false,

    // Environment configuration
    'environment' => env('APP_ENV', 'production'),

    // Release tracking
    'release' => env('SENTRY_RELEASE', null),

    // Breadcrumbs configuration
    'breadcrumbs' => [
        // Capture SQL queries (without bindings for security)
        'sql_queries' => true,
        'sql_bindings' => false,

        // Capture queue job info
        'queue_info' => true,

        // Capture command info
        'command_info' => true,

        // Capture logs at warning level and above
        'logs' => true,
    ],

    // Before send callback configuration
    'before_send' => null,

    // Ignored exceptions (already handled gracefully)
    'ignore_exceptions' => [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Validation\ValidationException::class,
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
        \App\Exceptions\InsufficientBalanceException::class,
    ],

    // Ignored transactions
    'ignore_transactions' => [
        // Don't track health check endpoints
        'GET /health',
        'GET /api/health',
    ],
];
