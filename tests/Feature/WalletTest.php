<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Exception;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    protected WalletService $walletService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->walletService = new WalletService();
    }

    public function test_user_can_deposit_funds()
    {
        $user = User::factory()->create(['balance' => 0]);

        $this->walletService->deposit($user, 100.00, 'Test Deposit');

        $this->assertEquals(100.00, $user->refresh()->balance);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => 100.00,
            'description' => 'Test Deposit',
        ]);
    }

    public function test_user_can_withdraw_funds()
    {
        $user = User::factory()->create(['balance' => 200.00]);

        $this->walletService->withdraw($user, 50.00, 'Test Withdrawal');

        $this->assertEquals(150.00, $user->refresh()->balance);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'type' => 'withdrawal',
            'amount' => -50.00,
        ]);
    }

    public function test_cannot_withdraw_insufficient_funds()
    {
        $user = User::factory()->create(['balance' => 10.00]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient balance');

        $this->walletService->withdraw($user, 20.00);
    }
}
