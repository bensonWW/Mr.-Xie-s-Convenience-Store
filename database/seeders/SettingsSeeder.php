<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'free_shipping_threshold'],
            [
                'value' => 1000,
                'description' => 'Order amount required for free shipping'
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'shipping_fee'],
            [
                'value' => 60,
                'description' => 'Standard shipping fee'
            ]
        );
    }
}
