<?php

namespace App\Services;

use App\Models\Setting;

class SystemConfigService
{
    /**
     * Get the free shipping threshold in cents.
     * 
     * @return int
     */
    public function getFreeShippingThreshold(): int
    {
        // Default: 1000 threshold (in whatever unit was used before, likely dollars/yuan)
        // If the setting in DB is still '1000' (currency), we might need to convert it here or expect it to be updated.
        // Assuming settings are stored as raw numbers. If logic requires 1000 * 100 for cents:
        return (int) Setting::get('free_shipping_threshold', 1000);
    }

    /**
     * Get the standard shipping fee in cents.
     * 
     * @return int
     */
    public function getShippingFee(): int
    {
        return (int) Setting::get('shipping_fee', 60);
    }
}
