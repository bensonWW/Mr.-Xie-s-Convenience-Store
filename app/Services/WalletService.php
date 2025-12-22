<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
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
     * Process a wallet transaction with pessimistic locking.
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

            // Check sufficient funds for processing negative amounts (withdrawals/payments)
            if ($amount < 0 && $lockedUser->balance < abs($amount)) {
                throw new \App\Exceptions\InsufficientBalanceException("Insufficient balance.");
            }

            $lockedUser->balance += $amount;
            $lockedUser->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => $type,
                'amount' => $amount,
                'description' => $description,
                'order_id' => $orderId,
                // reference_id and refund_id removed - using only order_id for all order-related transactions
            ]);
        });
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
