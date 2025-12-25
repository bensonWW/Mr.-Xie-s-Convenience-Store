<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WalletLog;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletAuditTest extends TestCase
{
    use RefreshDatabase;

    protected WalletService $walletService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->walletService = app(WalletService::class);
        $this->seed(\Database\Seeders\MemberLevelSeeder::class);
    }

    public function test_deposit_creates_audit_log(): void
    {
        $user = User::factory()->create(['balance' => 0]);

        $transaction = $this->walletService->deposit($user, 10000, 'Test deposit');

        $this->assertDatabaseHas('wallet_logs', [
            'wallet_transaction_id' => $transaction->id,
            'user_id' => $user->id,
            'action' => 'deposit',
            'amount' => 10000,
            'balance_before' => 0,
            'balance_after' => 10000,
        ]);
    }

    public function test_payment_creates_audit_log_with_correct_balance(): void
    {
        $user = User::factory()->create(['balance' => 50000]);

        $transaction = $this->walletService->pay($user, 20000, 'Test payment');

        $this->assertDatabaseHas('wallet_logs', [
            'wallet_transaction_id' => $transaction->id,
            'user_id' => $user->id,
            'action' => 'payment',
            'amount' => -20000,
            'balance_before' => 50000,
            'balance_after' => 30000,
        ]);
    }

    public function test_refund_creates_audit_log(): void
    {
        $user = User::factory()->create(['balance' => 10000]);

        $transaction = $this->walletService->refund($user, 5000, 'Refund order #123');

        $this->assertDatabaseHas('wallet_logs', [
            'wallet_transaction_id' => $transaction->id,
            'user_id' => $user->id,
            'action' => 'refund',
            'amount' => 5000,
            'balance_before' => 10000,
            'balance_after' => 15000,
            'reason' => 'Refund order #123',
        ]);
    }

    public function test_audit_log_has_checksum(): void
    {
        $user = User::factory()->create(['balance' => 0]);

        $transaction = $this->walletService->deposit($user, 10000);

        $log = WalletLog::where('wallet_transaction_id', $transaction->id)->first();

        $this->assertNotNull($log->checksum);
        $this->assertTrue($log->verifyChecksum());
    }

    public function test_checksum_detects_tampering(): void
    {
        $user = User::factory()->create(['balance' => 0]);

        $transaction = $this->walletService->deposit($user, 10000);

        $log = WalletLog::where('wallet_transaction_id', $transaction->id)->first();

        // Tamper with the amount
        $log->amount = 99999;

        $this->assertFalse($log->verifyChecksum());
    }

    public function test_admin_deposit_records_operator(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'balance' => 0]);
        $user = User::factory()->create(['balance' => 0]);

        // Simulate admin authentication
        $this->actingAs($admin);

        $transaction = $this->walletService->deposit($user, 10000, 'Admin top-up');

        $this->assertDatabaseHas('wallet_logs', [
            'wallet_transaction_id' => $transaction->id,
            'user_id' => $user->id,
            'operator_id' => $admin->id,
            'operator_type' => 'admin',
        ]);
    }

    public function test_user_self_deposit_records_user_operator_type(): void
    {
        $user = User::factory()->create(['balance' => 0]);

        // Simulate user authentication
        $this->actingAs($user);

        $transaction = $this->walletService->deposit($user, 10000);

        $this->assertDatabaseHas('wallet_logs', [
            'wallet_transaction_id' => $transaction->id,
            'user_id' => $user->id,
            'operator_id' => $user->id,
            'operator_type' => 'user',
        ]);
    }
}
