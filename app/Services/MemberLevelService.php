<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;

class MemberLevelService
{
    /**
     * Calculate discount for a user based on subtotal.
     *
     * @param User $user
     * @param float $subtotal
     * @return float
     */
    public function calculateDiscount(User $user, float $subtotal): float
    {
        $level = $user->member_level ?? 'normal';
        $config = config("shop.levels.{$level}");

        if (!$config || !isset($config['discount'])) {
            return 0.00;
        }

        return round($subtotal * $config['discount'], 2);
    }

    /**
     * Check and upgrade user member level based on total spent.
     *
     * @param User $user
     * @return void
     */
    public function checkAndUpgrade(User $user): void
    {
        // Calculate total spent from COMPLETED or PROCESSING (Paid) orders
        $totalSpent = Order::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'processing'])
            ->sum('total_amount');

        $levels = config('shop.levels');

        // Sort levels by threshold descending
        uasort($levels, function ($a, $b) {
            return $b['threshold'] <=> $a['threshold'];
        });

        $currentLevel = $user->member_level;
        $newLevel = 'normal';

        foreach ($levels as $key => $data) {
            if ($totalSpent >= $data['threshold']) {
                $newLevel = $key;
                break;
            }
        }

        // Update if level changed
        if ($newLevel !== $currentLevel) {
            $user->member_level = $newLevel;
            $user->save();
        }
    }
}
