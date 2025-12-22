<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->user_id || $user->isAdmin();
    }

    public function refund(User $user, Order $order): bool
    {
        return $user->id === $order->user_id || $user->isAdmin();
    }
}
