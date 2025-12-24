<?php

namespace Tests\Feature;

use App\Models\MemberLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberLevelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed member levels
        MemberLevel::firstOrCreate(
            ['slug' => 'normal'],
            ['name' => 'Normal', 'threshold' => 0, 'discount' => 0.00]
        );
        MemberLevel::firstOrCreate(
            ['slug' => 'vip'],
            ['name' => 'VIP', 'threshold' => 1000, 'discount' => 0.05]
        );
    }

    public function test_admin_can_lock_member_level()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $normalLevel = MemberLevel::where('slug', 'normal')->first();
        $vipLevel = MemberLevel::where('slug', 'vip')->first();
        $user = User::factory()->create(['member_level_id' => $normalLevel->id, 'is_level_locked' => false]);

        // API expects member_level as slug string, not member_level_id
        $response = $this->actingAs($admin)->putJson("/api/admin/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'member_level' => 'vip',  // Send slug, API converts to member_level_id
            'is_level_locked' => true,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'member_level_id' => $vipLevel->id,
            'is_level_locked' => 1, // boolean true is 1 in MySQL
        ]);
    }

    public function test_is_level_locked_defaults_to_false()
    {
        $user = User::factory()->create();
        $this->assertFalse((bool)$user->is_level_locked);
    }
}
