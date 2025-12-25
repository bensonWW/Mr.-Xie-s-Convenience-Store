<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WalletLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class WalletService
{
    /**
     * Deposit funds into user's wallet.
     *
     * @param User $user
     * @param int $amount Amount in cents
     * @param string|null $description
     * @param int|null $orderId Related order ID (if applicable)
     * @return WalletTransaction
     * @throws Exception
     */
    public function deposit(User $user, int $amount, ?string $description = null, ?int $orderId = null): WalletTransaction
    {
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
     * @throws Exception
     */
    public function withdraw(User $user, int $amount, ?string $description = null, ?int $orderId = null): WalletTransaction
    {
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
     * @throws Exception
     */
    public function pay(User $user, int $amount, ?string $description = null, ?int $orderId = null): WalletTransaction
    {
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
     * @throws Exception
     */
    public function refund(User $user, int $amount, ?string $description = null, ?int $orderId = null): WalletTransaction
    {
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
     * @throws Exception
     */
    public function adjust(User $user, int $amount, string $reason): WalletTransaction
    {
        if (empty($reason)) {
            throw new Exception("Adjustment reason is required.");
        }

        return $this->processTransaction($user, $amount, 'adjustment', $reason);
    }

    /**
     * Process a wallet transaction with pessimistic locking.
     * Now includes audit logging (ADR-008)
     */
    protected function processTransaction(
        User $user,
        int $amount,
        string $type,
        ?string $description = null,
        ?int $orderId = null
    ): WalletTransaction {
        if ($amount === 0) {
            throw new Exception("Transaction amount cannot be zero.");
        }

        return DB::transaction(function () use ($user, $amount, $type, $description, $orderId) {
            // Pessimistic Lock
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            // Record balance before transaction
            $balanceBefore = $lockedUser->balance;

            // Check sufficient funds for processing negative amounts (withdrawals/payments)
            if ($amount < 0 && $lockedUser->balance < abs($amount)) {
                throw new \App\Exceptions\InsufficientBalanceException("Insufficient balance.");
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
                $description
            );

            return $transaction;
        });
    }

    /**
     * Create audit log entry for wallet transaction
     */
    protected function createAuditLog(
        WalletTransaction $transaction,
        User $user,
        string $action,
        int $amount,
        int $balanceBefore,
        int $balanceAfter,
        ?string $reason = null
    ): void {
        /** @var \App\Models\User|null $operator */
        $operator = Auth::user();
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

        WalletLog::create([
            'wallet_transaction_id' => $transaction->id,
            'user_id' => $user->id,
            'operator_id' => $operator?->id,
            'operator_type' => $operatorType,
            'action' => $action,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
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
