<?php

namespace App\Enums;

/**
 * User roles enum for consistent role checking.
 */
enum Role: string
{
    case ADMIN = 'admin';
    case CUSTOMER = 'customer';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::CUSTOMER => 'Customer',
        };
    }

    /**
     * Check if this role has admin privileges.
     */
    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }
}
