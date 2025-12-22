<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MemberLevel;

class MemberLevelSeeder extends Seeder
{
    /**
     * Seed the member_levels table.
     */
    public function run(): void
    {
        // Only seed if table is empty (avoid duplicates)
        if (MemberLevel::count() > 0) {
            return;
        }

        $levels = config('shop.levels', [
            'normal' => ['name' => '一般會員', 'threshold' => 0, 'discount' => 0.00],
            'vip' => ['name' => 'VIP 會員', 'threshold' => 1000, 'discount' => 0.05],
            'platinum' => ['name' => '白金會員', 'threshold' => 5000, 'discount' => 0.10],
        ]);

        foreach ($levels as $slug => $data) {
            MemberLevel::create([
                'slug' => $slug,
                'name' => $data['name'] ?? ucfirst($slug),
                'threshold' => $data['threshold'] ?? 0,
                'discount' => $data['discount'] ?? 0,
            ]);
        }
    }
}
