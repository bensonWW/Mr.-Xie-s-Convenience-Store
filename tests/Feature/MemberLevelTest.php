<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberLevelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_lock_member_level()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['member_level' => 'normal', 'is_level_locked' => false]);

        $response = $this->actingAs($admin)->putJson("/api/admin/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'member_level' => 'vip',
            'is_level_locked' => true,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'member_level' => 'vip',
            'is_level_locked' => 1, // boolean true is 1 in MySQL
        ]);
    }

    public function test_is_level_locked_defaults_to_false()
    {
        $user = User::factory()->create();
        $this->assertFalse((bool)$user->is_level_locked);
    }
}
