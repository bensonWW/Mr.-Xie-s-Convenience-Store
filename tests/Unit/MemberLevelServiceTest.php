<?php

namespace Tests\Unit;

use App\Models\MemberLevel;
use App\Models\Order;
use App\Models\User;
use App\Services\MemberLevelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberLevelServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MemberLevelService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MemberLevelService();
        $this->seedMemberLevels();
    }

    protected function seedMemberLevels(): void
    {
        MemberLevel::firstOrCreate(
            ['slug' => 'normal'],
            [
                'name' => '一般會員',
                'threshold' => 0,
                'discount' => 0,
            ]
        );
        MemberLevel::firstOrCreate(
            ['slug' => 'vip'],
            [
                'name' => 'VIP會員',
                'threshold' => 1000000, // 10000 元 = 1000000 cents
                'discount' => 0.05,     // 5% off
            ]
        );
        MemberLevel::firstOrCreate(
            ['slug' => 'vvip'],
            [
                'name' => 'VVIP會員',
                'threshold' => 5000000, // 50000 元
                'discount' => 0.10,     // 10% off
            ]
        );
    }

    // ===========================================
    // calculateDiscount Tests
    // ===========================================

    public function test_calculate_discount_normal_member_returns_zero()
    {
        $normalLevel = MemberLevel::findBySlug('normal');
        $user = User::factory()->create(['member_level_id' => $normalLevel->id]);

        $discount = $this->service->calculateDiscount($user, 100000); // 1000元

        $this->assertEquals(0, $discount);
    }

    public function test_calculate_discount_vip_member_returns_five_percent()
    {
        $vipLevel = MemberLevel::findBySlug('vip');
        $user = User::factory()->create(['member_level_id' => $vipLevel->id]);

        $discount = $this->service->calculateDiscount($user, 100000); // 1000元

        // 5% of 100000 = 5000
        $this->assertEquals(5000, $discount);
    }

    public function test_calculate_discount_vvip_member_returns_ten_percent()
    {
        $vvipLevel = MemberLevel::findBySlug('vvip');
        $user = User::factory()->create(['member_level_id' => $vvipLevel->id]);

        $discount = $this->service->calculateDiscount($user, 100000); // 1000元

        // 10% of 100000 = 10000
        $this->assertEquals(10000, $discount);
    }

    public function test_calculate_discount_rounds_to_nearest_cent()
    {
        $vipLevel = MemberLevel::findBySlug('vip');
        $user = User::factory()->create(['member_level_id' => $vipLevel->id]);

        // 5% of 12345 = 617.25 → rounds to 617
        $discount = $this->service->calculateDiscount($user, 12345);
        $this->assertEquals(617, $discount);

        // 5% of 12346 = 617.3 → rounds to 617
        $discount2 = $this->service->calculateDiscount($user, 12346);
        $this->assertEquals(617, $discount2);

        // 5% of 12350 = 617.5 → rounds to 618
        $discount3 = $this->service->calculateDiscount($user, 12350);
        $this->assertEquals(618, $discount3);
    }

    public function test_calculate_discount_user_without_level_uses_normal()
    {
        $user = User::factory()->create(['member_level_id' => null]);

        $discount = $this->service->calculateDiscount($user, 100000);

        $this->assertEquals(0, $discount);
    }

    public function test_calculate_discount_zero_subtotal_returns_zero()
    {
        $vipLevel = MemberLevel::findBySlug('vip');
        $user = User::factory()->create(['member_level_id' => $vipLevel->id]);

        $discount = $this->service->calculateDiscount($user, 0);

        $this->assertEquals(0, $discount);
    }

    // ===========================================
    // checkAndUpgrade Tests
    // ===========================================

    public function test_check_and_upgrade_upgrades_to_vip_when_threshold_reached()
    {
        $normalLevel = MemberLevel::findBySlug('normal');
        $vipLevel = MemberLevel::findBySlug('vip');

        // Skip if VIP level doesn't exist
        if (!$vipLevel) {
            $this->markTestSkipped('VIP level not found in database');
        }

        $user = User::factory()->create(['member_level_id' => $normalLevel->id]);

        // Create orders totaling exactly VIP threshold
        Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'total_amount' => $vipLevel->threshold,
        ]);

        $this->service->checkAndUpgrade($user);

        $user->refresh();
        $this->assertEquals($vipLevel->id, $user->member_level_id);
    }

    public function test_check_and_upgrade_upgrades_to_highest_matching_level()
    {
        $normalLevel = MemberLevel::findBySlug('normal');
        // Get the highest threshold level
        $highestLevel = MemberLevel::orderBy('threshold', 'desc')->first();

        // Skip if no levels with threshold > 0
        if ($highestLevel->threshold == 0) {
            $this->markTestSkipped('No premium levels found in database');
        }

        $user = User::factory()->create(['member_level_id' => $normalLevel->id]);

        // Create orders totaling highest threshold
        Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'total_amount' => $highestLevel->threshold,
        ]);

        $this->service->checkAndUpgrade($user);

        $user->refresh();
        $this->assertEquals($highestLevel->id, $user->member_level_id);
    }

    public function test_check_and_upgrade_does_not_upgrade_if_locked()
    {
        $normalLevel = MemberLevel::findBySlug('normal');
        $user = User::factory()->create([
            'member_level_id' => $normalLevel->id,
            'is_level_locked' => true,
        ]);

        Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'total_amount' => 5000000,
        ]);

        $this->service->checkAndUpgrade($user);

        $user->refresh();
        $this->assertEquals($normalLevel->id, $user->member_level_id);
    }

    public function test_check_and_upgrade_ignores_cancelled_orders()
    {
        $normalLevel = MemberLevel::findBySlug('normal');
        $user = User::factory()->create(['member_level_id' => $normalLevel->id]);

        // Cancelled orders should not count
        Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'cancelled',
            'total_amount' => 5000000,
        ]);

        $this->service->checkAndUpgrade($user);

        $user->refresh();
        $this->assertEquals($normalLevel->id, $user->member_level_id);
    }

    public function test_check_and_upgrade_counts_processing_orders()
    {
        $normalLevel = MemberLevel::findBySlug('normal');
        $vipLevel = MemberLevel::findBySlug('vip');

        if (!$vipLevel) {
            $this->markTestSkipped('VIP level not found in database');
        }

        $user = User::factory()->create(['member_level_id' => $normalLevel->id]);

        // Processing (paid) orders should count
        Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'processing',
            'total_amount' => $vipLevel->threshold,
        ]);

        $this->service->checkAndUpgrade($user);

        $user->refresh();
        $this->assertEquals($vipLevel->id, $user->member_level_id);
    }

    // ===========================================
    // getAllLevels Tests
    // ===========================================

    public function test_get_all_levels_returns_sorted_by_threshold()
    {
        $levels = $this->service->getAllLevels();

        // Verify levels are sorted by threshold ascending
        $this->assertGreaterThanOrEqual(3, $levels->count());

        // Verify ordering - each level should have threshold >= previous
        $prevThreshold = -1;
        foreach ($levels as $level) {
            $this->assertGreaterThanOrEqual($prevThreshold, $level->threshold);
            $prevThreshold = $level->threshold;
        }

        // Verify our expected slugs exist
        $slugs = $levels->pluck('slug')->toArray();
        $this->assertContains('normal', $slugs);
        $this->assertContains('vip', $slugs);
        $this->assertContains('vvip', $slugs);
    }
}
