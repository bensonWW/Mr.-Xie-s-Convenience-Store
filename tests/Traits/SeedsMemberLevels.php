<?php

namespace Tests\Traits;

use App\Models\MemberLevel;

/**
 * Trait for seeding member levels in tests.
 * Include this in any test that creates orders or uses member discount logic.
 */
trait SeedsMemberLevels
{
    protected function seedMemberLevels(): void
    {
        $levels = [
            ['slug' => 'normal', 'name' => 'Normal', 'threshold' => 0, 'discount' => 0.00],
            ['slug' => 'vip', 'name' => 'VIP', 'threshold' => 1000, 'discount' => 0.05],
            ['slug' => 'gold', 'name' => 'Gold', 'threshold' => 3000, 'discount' => 0.08],
            ['slug' => 'platinum', 'name' => 'Platinum', 'threshold' => 5000, 'discount' => 0.10],
            ['slug' => 'diamond', 'name' => 'Diamond', 'threshold' => 10000, 'discount' => 0.15],
        ];

        foreach ($levels as $level) {
            MemberLevel::firstOrCreate(['slug' => $level['slug']], $level);
        }
    }

    protected function getMemberLevel(string $slug): MemberLevel
    {
        return MemberLevel::where('slug', $slug)->firstOrFail();
    }
}
