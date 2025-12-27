<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\MemberLevel;

class MemberLevelService
{
    /**
     * Calculate discount for a user based on subtotal (in cents).
     *
     * @param User $user
     * @param int $subtotal
     * @return int
     */
    public function calculateDiscount(User $user, int $subtotal): int
    {
        // Get discount from the normalized MemberLevel relationship
        $level = $user->memberLevel;

        if (!$level) {
            // Fallback: get default normal level
            $level = MemberLevel::findBySlug('normal');
        }

        // If still no level found or no discount defined, return 0
        if (!$level) {
            return 0;
        }

        $discount = $level->discount ?? 0;
        if ($discount <= 0) {
            return 0;
        }

        // Discount is a decimal e.g. 0.05 for 5% off.
        // Use bcmul for precision, then round to nearest cent.
        return (int) round((float) bcmul((string) $subtotal, (string) $discount, 4));
    }

    /**
     * Check and upgrade user member level based on total spent.
     *
     * @param User $user
     * @return void
     */
    public function checkAndUpgrade(User $user): void
    {
        // Skip if level is locked by admin
        if ($user->is_level_locked) {
            return;
        }

        // Calculate total spent from COMPLETED or PROCESSING (Paid) orders
        $totalSpent = Order::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'processing'])
            ->sum('total_amount');

        // Get all levels sorted by threshold descending
        $levels = MemberLevel::orderBy('threshold', 'desc')->get();

        $newLevel = null;
        foreach ($levels as $level) {
            if ($totalSpent >= $level->threshold) {
                $newLevel = $level;
                break;
            }
        }

        // Fallback to normal if no level matched
        if (!$newLevel) {
            $newLevel = MemberLevel::findBySlug('normal');
        }

        // Update if level changed
        if ($newLevel && $user->member_level_id !== $newLevel->id) {
            $user->member_level_id = $newLevel->id;
            $user->save();
        }
    }

    /**
     * Get all available member levels.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllLevels()
    {
        return MemberLevel::orderBy('threshold', 'asc')->get();
    }
}
