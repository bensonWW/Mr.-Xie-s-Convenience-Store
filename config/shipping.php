<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shipping Fee Configuration
    |--------------------------------------------------------------------------
    |
    | Define shipping zones and their corresponding fees.
    | Fees are stored in cents (分) for precision.
    |
    */

    // Default flat rate shipping fee (in cents)
    'default_fee' => env('SHIPPING_DEFAULT_FEE', 6000), // 60元

    // Free shipping threshold (in cents) - orders above this get free shipping
    'free_shipping_threshold' => env('SHIPPING_FREE_THRESHOLD', 100000), // 1000元

    /*
    |--------------------------------------------------------------------------
    | Shipping Zones
    |--------------------------------------------------------------------------
    |
    | Define regions with custom shipping fees.
    | Priority: Use zone fee if matched, otherwise use default_fee.
    |
    */
    'zones' => [
        'taipei' => [
            'name' => '台北市',
            'cities' => ['台北市', '新北市', '基隆市'],
            'fee' => 5000, // 50元
        ],
        'central' => [
            'name' => '中部地區',
            'cities' => ['台中市', '彰化縣', '南投縣', '苗栗縣', '雲林縣'],
            'fee' => 6000, // 60元
        ],
        'south' => [
            'name' => '南部地區',
            'cities' => ['高雄市', '台南市', '嘉義市', '嘉義縣', '屏東縣'],
            'fee' => 6500, // 65元
        ],
        'east' => [
            'name' => '東部地區',
            'cities' => ['宜蘭縣', '花蓮縣', '台東縣'],
            'fee' => 8000, // 80元
        ],
        'islands' => [
            'name' => '離島地區',
            'cities' => ['澎湖縣', '金門縣', '連江縣'],
            'fee' => 15000, // 150元
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tiered Shipping (階梯運費)
    |--------------------------------------------------------------------------
    |
    | Optional: Different fees based on order subtotal.
    | If enabled, overrides zone fees.
    |
    */
    'tiered' => [
        'enabled' => env('SHIPPING_TIERED_ENABLED', false),
        'tiers' => [
            // subtotal_min => fee (in cents)
            0 => 8000,      // 0-499元: 80元運費
            50000 => 6000,  // 500-999元: 60元運費
            100000 => 0,    // 1000元以上: 免運
        ],
    ],
];
