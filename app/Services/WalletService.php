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
     * @param float $amount
     * @param string|null $description
     * @param string|null $referenceId
     * @return WalletTransaction
     * @throws Exception
     */
    public function deposit(User $user, float $amount, ?string $description = null, ?string $referenceId = null): WalletTransaction
    {
        return $this->processTransaction($user, $amount, 'deposit', $description, $referenceId);
    }

    /**
     * Withdraw funds from user's wallet.
     *
     * @param User $user
     * @param float $amount
     * @param string|null $description
     * @param string|null $referenceId
     * @return WalletTransaction
     * @throws Exception
     */
    public function withdraw(User $user, float $amount, ?string $description = null, ?string $referenceId = null): WalletTransaction
    {
        return $this->processTransaction($user, -$amount, 'withdrawal', $description, $referenceId);
    }

    /**
     * Refund funds to user's wallet.
     *
     * @param User $user
     * @param float $amount
     * @param string|null $description
     * @param string|null $referenceId
     * @return WalletTransaction
     * @throws Exception
     */
    public function refund(User $user, float $amount, ?string $description = null, ?string $referenceId = null): WalletTransaction
    {
        return $this->processTransaction($user, $amount, 'refund', $description, $referenceId);
    }

    /**
     * Core transaction processing logic.
     *
     * @param User $user
     * @param float $amount Positive for credit, Negative for debit.
     * @param string $type
     * @param string|null $description
     * @param string|null $referenceId
     * @return WalletTransaction
     * @throws Exception
     */
    protected function processTransaction(User $user, float $amount, string $type, ?string $description = null, ?string $referenceId = null): WalletTransaction
    {
        if ($amount == 0) {
            throw new Exception("Transaction amount cannot be zero.");
        }

        return DB::transaction(function () use ($user, $amount, $type, $description, $referenceId) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            // Check sufficient funds for processing negative amounts (withdrawals)
            if ($amount < 0 && $lockedUser->balance < abs($amount)) {
                throw new Exception("Insufficient balance.");
            }

            $lockedUser->balance += $amount;
            $lockedUser->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => $type,
                'amount' => $amount,
                'description' => $description,
                'reference_id' => $referenceId,
            ]);
        });
    }

    /**
     * Get current balance.
     *
     * @param User $user
     * @return float
     */
    public function getBalance(User $user): float
    {
        return (float) $user->refresh()->balance;
    }
}
