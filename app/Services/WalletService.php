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
        if ($amount <= 0) {
            throw new Exception("Deposit amount must be positive.");
        }

        return DB::transaction(function () use ($user, $amount, $description, $referenceId) {
            // Lock the user row for update to prevent race conditions
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            $lockedUser->balance += $amount;
            $lockedUser->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'deposit',
                'amount' => $amount,
                'description' => $description,
                'reference_id' => $referenceId,
            ]);
        });
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
        if ($amount <= 0) {
            throw new Exception("Withdrawal amount must be positive.");
        }

        return DB::transaction(function () use ($user, $amount, $description, $referenceId) {
            // Lock the user row for update
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            if ($lockedUser->balance < $amount) {
                throw new Exception("Insufficient balance.");
            }

            $lockedUser->balance -= $amount;
            $lockedUser->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'withdrawal', // or 'payment'
                'amount' => -$amount, // Store as negative for consistency in some views, or handle logic elsewhere. Detailed logs usually store absolute amount with type. Let's store absolute amount and rely on type, or match the schema decision.
                // Re-reading schema: type is string. standard accounting: Debit/Credit. 
                // Let's store logic: amount in DB is 'decimal'. 
                // Usually transactions show movement. 
                // Let's keep it simple: Transaction stores the IMPACT on balance? Or just the magnitude?
                // Given `balance += amount` logic above, usually explicit types are better. 
                // Let's store the signed amount for easier summation? 
                // "balance = sum(transactions)" is a good check. 
                // If I store -50.00 for withdrawal, sum works. 
                // Let's store NEGATIVE for withdrawal.
                'amount' => -$amount,
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
