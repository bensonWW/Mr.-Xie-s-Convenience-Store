<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminWalletTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user if not handled by factory properly assign role
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'customer']);
    }

    public function test_admin_can_deposit_funds_to_user()
    {
        $response = $this->actingAs($this->admin)
            ->postJson("/api/admin/users/{$this->user->id}/wallet/transaction", [
                'type' => 'deposit',
                'amount' => 500, // 500 dollars
                'description' => 'Admin Bonus'
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'balance' => 50000 // 500 dollars = 50000 cents
        ]);
    }

    public function test_admin_can_withdraw_funds_from_user()
    {
        // Balance is in cents, API amount is in dollars (converted to cents)
        // Set balance to 100000 cents ($1000) so we can withdraw 300 dollars (30000 cents)
        $this->user->forceFill(['balance' => 100000])->save();
        $this->user->refresh();

        $response = $this->actingAs($this->admin)
            ->postJson("/api/admin/users/{$this->user->id}/wallet/transaction", [
                'type' => 'withdraw',
                'amount' => 300, // 300 dollars = 30000 cents
                'description' => 'Admin Correction'
            ]);

        if ($response->status() !== 200) dump($response->json());
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'balance' => 70000 // 100000 - 30000 = 70000 cents
        ]);
    }

    public function test_admin_cannot_withdraw_insufficient_funds()
    {
        // Balance is 10000 cents ($100), try to withdraw 500 dollars (50000 cents)
        $this->user->update(['balance' => 10000]);

        $response = $this->actingAs($this->admin)
            ->postJson("/api/admin/users/{$this->user->id}/wallet/transaction", [
                'type' => 'withdraw',
                'amount' => 500, // 500 dollars = 50000 cents, more than 10000
                'description' => 'Admin Correction'
            ]);

        $response->assertStatus(400);
    }
}
