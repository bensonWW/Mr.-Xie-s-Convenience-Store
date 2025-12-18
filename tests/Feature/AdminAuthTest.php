<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_routes()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/api/admin/users');

        $response->assertStatus(200);
    }

    public function test_customer_cannot_access_admin_routes()
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user)->getJson('/api/admin/users');

        // Assuming middleware returns 403
        $response->assertStatus(403);
    }
}
