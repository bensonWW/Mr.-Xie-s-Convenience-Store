<?php

namespace Tests\Feature;

use App\Models\MemberLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberLevelSeedingTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_levels_are_seeded()
    {
        $count = MemberLevel::count();
        $this->assertGreaterThan(0, $count, "MemberLevel table should have records, found: {$count}");

        $normal = MemberLevel::findBySlug('normal');
        $this->assertNotNull($normal, 'Normal level should exist');

        $vip = MemberLevel::findBySlug('vip');
        $this->assertNotNull($vip, 'VIP level should exist');
    }
}
