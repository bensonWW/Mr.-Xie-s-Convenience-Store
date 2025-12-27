<?php

return [
    'levels' => [
        'normal' => [
            'name' => '一般會員',
            'threshold' => 0,
            'discount' => 0.00,
        ],
        'vip' => [
            'name' => 'VIP 會員',
            'threshold' => 1000,
            'discount' => 0.05,
        ],
        'platinum' => [
            'name' => '白金會員',
            'threshold' => 5000,
            'discount' => 0.10,
        ],
    ],
];
