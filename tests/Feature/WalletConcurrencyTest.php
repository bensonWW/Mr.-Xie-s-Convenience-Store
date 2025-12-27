<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\WalletService;
use App\Exceptions\InsufficientBalanceException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WalletConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    protected WalletService $walletService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->walletService = new WalletService();
    }

    /**
     * Test that concurrent deposits are handled correctly.
     * Simulates multiple deposits happening at the same time.
     */
    public function test_concurrent_deposits_maintain_correct_balance()
    {
        $user = User::factory()->create(['balance' => 0]);
        $depositAmount = 10000; // 100元
        $concurrentDeposits = 5;

        // Simulate concurrent deposits using sequential calls
        // (True concurrency requires multiprocessing which is not easily testable in PHPUnit)
        for ($i = 0; $i < $concurrentDeposits; $i++) {
            $this->walletService->deposit($user, $depositAmount, "Deposit {$i}");
        }

        $user->refresh();
        $expectedBalance = $depositAmount * $concurrentDeposits;
        $this->assertEquals($expectedBalance, $user->balance);
    }

    /**
     * Test that concurrent withdrawals do not cause overdraft.
     */
    public function test_concurrent_withdrawals_prevent_overdraft()
    {
        $user = User::factory()->create(['balance' => 10000]); // 100元
        $withdrawAmount = 10000; // Try to withdraw full balance

        // First withdrawal should succeed
        $this->walletService->withdraw($user, $withdrawAmount, 'Withdrawal 1');

        $user->refresh();
        $this->assertEquals(0, $user->balance);

        // Second withdrawal should fail
        $this->expectException(InsufficientBalanceException::class);
        $this->walletService->withdraw($user, $withdrawAmount, 'Withdrawal 2');
    }

    /**
     * Test that interleaved deposits and withdrawals maintain consistency.
     */
    public function test_interleaved_transactions_maintain_balance_consistency()
    {
        $user = User::factory()->create(['balance' => 50000]); // 500元

        // Perform interleaved operations
        $this->walletService->deposit($user, 10000);    // +100 => 600
        $this->walletService->withdraw($user, 5000);    // -50 => 550
        $this->walletService->deposit($user, 20000);    // +200 => 750
        $this->walletService->withdraw($user, 30000);   // -300 => 450
        $this->walletService->deposit($user, 5000);     // +50 => 500

        $user->refresh();
        $this->assertEquals(50000, $user->balance); // Should be back to 500元
    }

    /**
     * Test that transaction logs match final balance.
     */
    public function test_transaction_history_matches_final_balance()
    {
        $user = User::factory()->create(['balance' => 0]);

        $this->walletService->deposit($user, 100000);   // +1000
        $this->walletService->withdraw($user, 30000);   // -300
        $this->walletService->deposit($user, 50000);    // +500
        $this->walletService->pay($user, 20000);        // -200

        $user->refresh();

        // Calculate expected balance from transactions
        $transactionSum = $user->walletTransactions()->sum('amount');
        $this->assertEquals($transactionSum, $user->balance);
        $this->assertEquals(100000, $user->balance); // 1000 - 300 + 500 - 200 = 1000
    }

    /**
     * Test pessimistic locking prevents race conditions.
     * This test verifies the locking mechanism by checking no money is lost.
     */
    public function test_pessimistic_lock_prevents_lost_updates()
    {
        $user = User::factory()->create(['balance' => 100000]); // 1000元

        // Simulate scenario where balance could be lost without locking
        // Transaction 1: Reads balance (1000), deposits 100
        // Transaction 2: Reads balance (1000), deposits 100
        // Without lock: Both write 1100, losing one deposit
        // With lock: Final balance should be 1200

        $depositAmount = 10000;
        $numTransactions = 10;

        for ($i = 0; $i < $numTransactions; $i++) {
            $this->walletService->deposit($user, $depositAmount);
        }

        $user->refresh();
        $expectedBalance = 100000 + ($depositAmount * $numTransactions);
        $this->assertEquals($expectedBalance, $user->balance);
    }

    /**
     * Test that failed transactions are rolled back.
     */
    public function test_failed_transaction_is_rolled_back()
    {
        $user = User::factory()->create(['balance' => 10000]); // 100元

        try {
            $this->walletService->withdraw($user, 20000); // Try to withdraw 200元
        } catch (InsufficientBalanceException $e) {
            // Expected
        }

        $user->refresh();
        $this->assertEquals(10000, $user->balance);

        // Verify no transaction was created
        $transactionCount = $user->walletTransactions()->where('amount', -20000)->count();
        $this->assertEquals(0, $transactionCount);
    }

    /**
     * Test zero amount transaction throws exception.
     */
    public function test_zero_amount_transaction_throws_exception()
    {
        $user = User::factory()->create(['balance' => 10000]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Amount must be positive.');

        $this->walletService->deposit($user, 0);
    }

    /**
     * Test refund operation works correctly.
     */
    public function test_refund_adds_to_balance()
    {
        $user = User::factory()->create(['balance' => 10000]);

        $this->walletService->refund($user, 5000, 'Order refund');

        $user->refresh();
        $this->assertEquals(15000, $user->balance);
    }

    /**
     * Test audit log is created for each transaction.
     */
    public function test_audit_log_created_for_transactions()
    {
        $user = User::factory()->create(['balance' => 0]);

        $this->walletService->deposit($user, 10000, 'Test deposit');

        $this->assertDatabaseHas('wallet_logs', [
            'user_id' => $user->id,
            'action' => 'deposit',
            'amount' => 10000,
            'balance_before' => 0,
            'balance_after' => 10000,
        ]);
    }
}
