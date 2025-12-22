<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure member_levels table is seeded for all tests
        $this->seedMemberLevels();
    }

    /**
     * Seed member levels if the table is empty.
     */
    protected function seedMemberLevels(): void
    {
        // Only run if the table exists and is empty
        if (!\Illuminate\Support\Facades\Schema::hasTable('member_levels')) {
            return;
        }

        if (\App\Models\MemberLevel::count() > 0) {
            return;
        }

        $levels = [
            ['slug' => 'normal', 'name' => '一般會員', 'threshold' => 0, 'discount' => 0.00],
            ['slug' => 'vip', 'name' => 'VIP 會員', 'threshold' => 1000, 'discount' => 0.05],
            ['slug' => 'gold', 'name' => '黃金會員', 'threshold' => 3000, 'discount' => 0.08],
            ['slug' => 'platinum', 'name' => '白金會員', 'threshold' => 5000, 'discount' => 0.10],
            ['slug' => 'diamond', 'name' => '鑽石會員', 'threshold' => 10000, 'discount' => 0.15],
        ];

        foreach ($levels as $level) {
            \App\Models\MemberLevel::create($level);
        }
    }
}
