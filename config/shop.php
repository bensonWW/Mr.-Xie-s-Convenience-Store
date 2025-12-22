<?php

return [
    'levels' => [
        'normal' => [
            'name' => '一般會員',
            'threshold' => 0,
            'discount' => 0.00, // 0%
        ],
        'vip' => [
            'name' => 'VIP 會員',
            'threshold' => 1000,
            'discount' => 0.05, // 5%
        ],
        'platinum' => [ // Using 'platinum' to match plan examples, or could be 'vvip'
            'name' => '白金會員',
            'threshold' => 5000,
            'discount' => 0.10, // 10%
        ],
    ],

    // Auto-upgrade logic?
    // Listen to OrderCompleted event
];
