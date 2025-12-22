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
                'amount' => 500,
                'description' => 'Admin Bonus'
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'balance' => 500
        ]);
    }

    public function test_admin_can_withdraw_funds_from_user()
    {
        $this->user->refresh();
        $this->user->forceFill(['balance' => 1000])->save();
        $this->user->refresh(); // Just to be sure
        // dump("User Balance Before Test: " . $this->user->balance);

        $response = $this->actingAs($this->admin)
            ->postJson("/api/admin/users/{$this->user->id}/wallet/transaction", [
                'type' => 'withdraw',
                'amount' => 300,
                'description' => 'Admin Correction'
            ]);

        if ($response->status() !== 200) dump($response->json());
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'balance' => 700
        ]);
    }

    public function test_admin_cannot_withdraw_insufficient_funds()
    {
        $this->user->update(['balance' => 100]);

        $response = $this->actingAs($this->admin)
            ->postJson("/api/admin/users/{$this->user->id}/wallet/transaction", [
                'type' => 'withdraw',
                'amount' => 500,
                'description' => 'Admin Correction'
            ]);

        $response->assertStatus(400);
    }
}
