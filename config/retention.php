<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Data Retention Policy Configuration
    |--------------------------------------------------------------------------
    |
    | Defines how long different types of data should be retained.
    | Values are in days. Set to null to keep forever.
    |
    */

    // Soft-deleted records cleanup
    'soft_deletes' => [
        'enabled' => env('RETENTION_SOFT_DELETE_CLEANUP', true),
        'days' => env('RETENTION_SOFT_DELETE_DAYS', 90), // 90 days after soft delete
    ],

    // Order data retention
    'orders' => [
        // Keep order data for legal/tax compliance
        'minimum_retention_years' => 7,
        // Archive orders older than this (move to cold storage)
        'archive_after_years' => 2,
    ],

    // Session and token cleanup
    'sessions' => [
        'cleanup_enabled' => true,
        'expire_after_days' => 30,
    ],

    // Audit logs (wallet_logs, activity logs)
    'audit_logs' => [
        // Financial audit logs must be retained for compliance
        'wallet_logs_retention_years' => 7,
        // General activity logs
        'activity_logs_retention_days' => 365,
    ],

    // Verification codes
    'verification_codes' => [
        'cleanup_enabled' => true,
        'expire_after_hours' => 24,
    ],

    // Cart cleanup (abandoned carts)
    'carts' => [
        'cleanup_enabled' => true,
        'expire_after_days' => 30,
    ],

    // Sequences table cleanup (old daily sequences)
    'sequences' => [
        'cleanup_enabled' => true,
        'keep_days' => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup Configuration
    |--------------------------------------------------------------------------
    */
    'backup' => [
        'enabled' => env('BACKUP_ENABLED', true),
        'schedule' => 'daily', // Options: hourly, daily, weekly
        'keep_daily_backups' => 7,
        'keep_weekly_backups' => 4,
        'keep_monthly_backups' => 6,
        'destination' => env('BACKUP_DESTINATION', 'local'), // local, s3, google
    ],
];
