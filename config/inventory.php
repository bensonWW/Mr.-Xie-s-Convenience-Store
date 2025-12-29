<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Inventory Configuration
    |--------------------------------------------------------------------------
    |
    | Configure high-concurrency inventory management.
    |
    */

    // Use Redis for high-concurrency stock management
    // When enabled, stock checks use Redis Lua scripts for atomic operations
    // Database remains the source of truth
    'redis_enabled' => env('INVENTORY_REDIS_ENABLED', false),

    // Sync strategy: 'immediate' or 'queued'
    // 'immediate' - sync Redis on every stock change
    // 'queued' - batch sync via scheduled job
    'sync_strategy' => env('INVENTORY_SYNC_STRATEGY', 'immediate'),

    // Low stock threshold for alerts
    'low_stock_threshold' => env('INVENTORY_LOW_STOCK_THRESHOLD', 10),

    // Products with stock below this are hidden from listing
    'out_of_stock_visibility' => env('INVENTORY_SHOW_OUT_OF_STOCK', true),

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    */

    // Maximum reservations per second (rate limiting)
    'max_reservations_per_second' => env('INVENTORY_MAX_TPS', 1000),

    // Reservation timeout in seconds (for cleanup of abandoned carts)
    'reservation_timeout_seconds' => 900, // 15 minutes
];
