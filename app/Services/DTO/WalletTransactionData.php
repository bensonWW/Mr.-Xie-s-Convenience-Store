<?php

namespace App\Services\DTO;

use App\Models\User;

/**
 * Data Transfer Object for wallet transactions.
 * Encapsulates all parameters needed for processing a wallet transaction.
 */
readonly class WalletTransactionData
{
    public function __construct(
        public User $user,
        public int $amount,
        public string $type,
        public ?string $description = null,
        public ?int $orderId = null,
        public ?User $operator = null,
    ) {}

    /**
     * Create a deposit transaction DTO.
     */
    public static function deposit(
        User $user,
        int $amount,
        ?string $description = null,
        ?int $orderId = null
    ): self {
        return new self(
            user: $user,
            amount: $amount,
            type: 'deposit',
            description: $description,
            orderId: $orderId,
        );
    }

    /**
     * Create a withdrawal transaction DTO.
     */
    public static function withdrawal(
        User $user,
        int $amount,
        ?string $description = null,
        ?int $orderId = null
    ): self {
        return new self(
            user: $user,
            amount: -abs($amount),
            type: 'withdrawal',
            description: $description,
            orderId: $orderId,
        );
    }

    /**
     * Create a payment transaction DTO.
     */
    public static function payment(
        User $user,
        int $amount,
        ?string $description = null,
        ?int $orderId = null
    ): self {
        return new self(
            user: $user,
            amount: -abs($amount),
            type: 'payment',
            description: $description,
            orderId: $orderId,
        );
    }

    /**
     * Create a refund transaction DTO.
     */
    public static function refund(
        User $user,
        int $amount,
        ?string $description = null,
        ?int $orderId = null
    ): self {
        return new self(
            user: $user,
            amount: abs($amount),
            type: 'refund',
            description: $description,
            orderId: $orderId,
        );
    }

    /**
     * Create an adjustment transaction DTO.
     */
    public static function adjustment(
        User $user,
        int $amount,
        string $reason,
        ?User $operator = null
    ): self {
        return new self(
            user: $user,
            amount: $amount,
            type: 'adjustment',
            description: $reason,
            orderId: null,
            operator: $operator,
        );
    }
}
