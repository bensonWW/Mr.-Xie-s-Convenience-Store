<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WalletLog;
use App\Services\DTO\WalletTransactionData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\InvalidWalletOperationException;
use App\Exceptions\InsufficientBalanceException;

class WalletService
{
    /**
     * Process a wallet transaction from a DTO.
     *
     * @throws InvalidWalletOperationException When amount is zero or invalid
     * @throws InsufficientBalanceException When balance is insufficient
     */
    public function processFromDTO(WalletTransactionData $dto): WalletTransaction
    {
        return $this->processTransaction(
            $dto->user,
            $dto->amount,
            $dto->type,
            $dto->description,
            $dto->orderId,
            $dto->operator
        );
    }

    /**
     * Deposit funds into user's wallet.
     *
     * @param User $user
     * @param int $amount Amount in cents
     * @param string|null $description
     * @param int|null $orderId Related order ID (if applicable)
     * @return WalletTransaction
     * @throws InvalidWalletOperationException When amount is zero
     */
    public function deposit(User $user, int $amount, ?string $description = null, ?int $orderId = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new InvalidWalletOperationException('Amount must be positive.');
        }

        return $this->processTransaction($user, $amount, 'deposit', $description, $orderId);
    }

    /**
     * Withdraw funds from user's wallet.
     *
     * @param User $user
     * @param int $amount Amount in cents
     * @param string|null $description
     * @param int|null $orderId Related order ID (if applicable)
     * @return WalletTransaction
     * @throws InvalidWalletOperationException When amount is zero
     * @throws InsufficientBalanceException When balance is insufficient
     */
    public function withdraw(User $user, int $amount, ?string $description = null, ?int $orderId = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new InvalidWalletOperationException('Amount must be positive.');
        }

        return $this->processTransaction($user, -$amount, 'withdrawal', $description, $orderId);
    }

    /**
     * Pay for an order using wallet balance.
     *
     * @param User $user
     * @param int $amount Amount in cents
     * @param string|null $description
     * @param int|null $orderId The order being paid for
     * @return WalletTransaction
     * @throws InvalidWalletOperationException When amount is zero
     * @throws InsufficientBalanceException When balance is insufficient
     */
    public function pay(User $user, int $amount, ?string $description = null, ?int $orderId = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new InvalidWalletOperationException('Amount must be positive.');
        }

        return $this->processTransaction($user, -$amount, 'payment', $description, $orderId);
    }

    /**
     * Refund funds to user's wallet.
     *
     * @param User $user
     * @param int $amount Amount in cents
     * @param string|null $description
     * @param int|null $orderId The order being refunded
     * @return WalletTransaction
     * @throws InvalidWalletOperationException When amount is zero
     */
    public function refund(User $user, int $amount, ?string $description = null, ?int $orderId = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new InvalidWalletOperationException('Amount must be positive.');
        }

        return $this->processTransaction($user, $amount, 'refund', $description, $orderId);
    }

    /**
     * Admin adjustment - can be positive or negative.
     * Used for manual balance corrections, promotions, penalties, etc.
     *
     * @param User $user
     * @param int $amount Amount in cents (positive for credit, negative for debit)
     * @param string $reason Required reason for the adjustment
     * @return WalletTransaction
     * @throws InvalidWalletOperationException When reason is empty or amount is zero
     * @throws InsufficientBalanceException When balance is insufficient for negative adjustment
     */
    public function adjust(User $user, int $amount, string $reason): WalletTransaction
    {
        if ($amount === 0) {
            throw new InvalidWalletOperationException('Adjustment amount cannot be zero.');
        }

        if (empty($reason)) {
            throw new InvalidWalletOperationException('Adjustment reason is required.');
        }

        return $this->processTransaction($user, $amount, 'adjustment', $reason);
    }

    /**
     * Process a wallet transaction with pessimistic locking.
     * Now includes audit logging (ADR-008)
     * 
     * @param User $user The user whose wallet is being modified
     * @param int $amount Amount in cents (positive or negative)
     * @param string $type Transaction type
     * @param string|null $description Description of transaction
     * @param int|null $orderId Related order ID
     * @param User|null $operator The user performing this action (null = system)
     */
    protected function processTransaction(
        User $user,
        int $amount,
        string $type,
        ?string $description = null,
        ?int $orderId = null,
        ?User $operator = null
    ): WalletTransaction {
        if ($amount === 0) {
            throw new InvalidWalletOperationException('Transaction amount cannot be zero.');
        }

        // Resolve operator: use provided, or try Auth, or null for system
        $operator = $operator ?? Auth::user();

        return DB::transaction(function () use ($user, $amount, $type, $description, $orderId, $operator) {
            // Pessimistic Lock
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            // Record balance before transaction
            $balanceBefore = $lockedUser->balance;

            // Check sufficient funds for processing negative amounts (withdrawals/payments)
            if ($amount < 0 && $lockedUser->balance < abs($amount)) {
                throw new InsufficientBalanceException('Insufficient balance.');
            }

            $lockedUser->balance += $amount;
            $lockedUser->save();

            $transaction = WalletTransaction::create([
                'user_id' => $user->id,
                'type' => $type,
                'amount' => $amount,
                'description' => $description,
                'order_id' => $orderId,
            ]);

            // Create audit log entry (ADR-008)
            $this->createAuditLog(
                $transaction,
                $user,
                $type,
                $amount,
                $balanceBefore,
                $lockedUser->balance,
                $description,
                $operator
            );

            return $transaction;
        });
    }

    /**
     * Create audit log entry for wallet transaction
     * 
     * @param WalletTransaction $transaction The transaction being logged
     * @param User $user The user whose wallet was modified
     * @param string $action The action type
     * @param int $amount The transaction amount
     * @param int $balanceBefore Balance before transaction
     * @param int $balanceAfter Balance after transaction
     * @param string|null $reason Reason for the transaction
     * @param User|null $operator The operator who initiated this (null = system)
     * @param string|null $ipAddress IP address (uses request if not provided)
     * @param string|null $userAgent User agent (uses request if not provided)
     */
    protected function createAuditLog(
        WalletTransaction $transaction,
        User $user,
        string $action,
        int $amount,
        int $balanceBefore,
        int $balanceAfter,
        ?string $reason = null,
        ?User $operator = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): void {
        $operatorType = 'user';

        if ($operator && $operator->id !== $user->id) {
            $operatorType = $operator->isAdmin() ? 'admin' : 'user';
        } elseif (!$operator) {
            $operatorType = 'system';
        }

        $checksum = WalletLog::generateChecksum(
            $transaction->id,
            $amount,
            $balanceBefore,
            $balanceAfter
        );

        // Fall back to request context if IP/UA not provided (for backward compatibility)
        $resolvedIp = $ipAddress ?? (app()->runningInConsole() ? null : request()->ip());
        $resolvedUserAgent = $userAgent ?? (app()->runningInConsole() ? null : request()->userAgent());

        WalletLog::create([
            'wallet_transaction_id' => $transaction->id,
            'user_id' => $user->id,
            'operator_id' => $operator?->id,
            'operator_type' => $operatorType,
            'action' => $action,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'ip_address' => $resolvedIp,
            'user_agent' => $resolvedUserAgent,
            'reason' => $reason,
            'checksum' => $checksum,
            'created_at' => now(),
        ]);
    }

    /**
     * Get current balance in cents.
     *
     * @param User $user
     * @return int
     */
    public function getBalance(User $user): int
    {
        return (int) $user->refresh()->balance;
    }
}
