<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_user_name_without_password()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'user@example.com',
            'password' => Hash::make('oldpassword')
        ]);

        $response = $this->actingAs($admin)->putJson("/api/admin/users/{$user->id}", [
            'name' => 'New Name',
            'email' => 'user@example.com',
            // No password sent
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);

        // Verify password is unchanged
        $user->refresh();
        $this->assertTrue(Hash::check('oldpassword', $user->password));
    }

    public function test_admin_can_update_user_password()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword')
        ]);

        $response = $this->actingAs($admin)->putJson("/api/admin/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'newpassword'
        ]);

        $response->assertStatus(200);

        // Verify password is changed
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword', $user->password));
    }
}
