<?php

namespace App\Services\DTO;

use App\Models\User;

class CreateOrderData
{
    public function __construct(
        public User $user,
        public ?string $couponCode = null,
        public ?string $shippingName = null,
        public ?string $shippingPhone = null,
        public ?string $shippingAddress = null,
        public ?string $paymentMethod = 'wallet', // Default to wallet
    ) {}

    /**
     * Create from Request if needed, but preferably manually instantiated in Controller to keep clean.
     */
}
