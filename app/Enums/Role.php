<?php

namespace App\Enums;

/**
 * User roles enum for consistent role checking.
 */
enum Role: string
{
    case ADMIN = 'admin';
    case STAFF = 'staff';
    case CUSTOMER = 'customer';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => '管理員',
            self::STAFF => '店員',
            self::CUSTOMER => '顧客',
        };
    }

    /**
     * Check if this role has admin privileges.
     */
    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Check if this role has staff privileges (admin or staff).
     */
    public function isStaff(): bool
    {
        return in_array($this, [self::ADMIN, self::STAFF]);
    }

    /**
     * Check if this role can access admin panel.
     */
    public function canAccessAdminPanel(): bool
    {
        return $this->isStaff();
    }
}
