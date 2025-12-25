<?php

namespace App\Services;

class ShippingService
{
    /**
     * Calculate shipping fee based on order subtotal and delivery address.
     *
     * @param int $subtotal Order subtotal in cents
     * @param string|null $city Delivery city name
     * @return int Shipping fee in cents
     */
    public function calculateFee(int $subtotal, ?string $city = null): int
    {
        // Check free shipping threshold first
        $freeThreshold = config('shipping.free_shipping_threshold', 100000);
        if ($subtotal >= $freeThreshold) {
            return 0;
        }

        // Check tiered pricing if enabled
        if (config('shipping.tiered.enabled', false)) {
            return $this->calculateTieredFee($subtotal);
        }

        // Zone-based pricing
        if ($city) {
            $zoneFee = $this->getZoneFee($city);
            if ($zoneFee !== null) {
                return $zoneFee;
            }
        }

        // Default fee
        return config('shipping.default_fee', 6000);
    }

    /**
     * Calculate tiered shipping fee based on subtotal.
     */
    protected function calculateTieredFee(int $subtotal): int
    {
        $tiers = config('shipping.tiered.tiers', []);

        // Sort tiers by threshold descending
        krsort($tiers);

        foreach ($tiers as $threshold => $fee) {
            if ($subtotal >= $threshold) {
                return $fee;
            }
        }

        // Return highest tier fee as fallback
        return end($tiers) ?: config('shipping.default_fee', 6000);
    }

    /**
     * Get shipping fee for a specific city/zone.
     */
    protected function getZoneFee(string $city): ?int
    {
        $zones = config('shipping.zones', []);

        foreach ($zones as $zone) {
            if (in_array($city, $zone['cities'] ?? [], true)) {
                return $zone['fee'];
            }
        }

        return null;
    }

    /**
     * Get all shipping zones with their fees.
     */
    public function getAllZones(): array
    {
        return config('shipping.zones', []);
    }

    /**
     * Get free shipping threshold.
     */
    public function getFreeShippingThreshold(): int
    {
        return config('shipping.free_shipping_threshold', 100000);
    }

    /**
     * Check if order qualifies for free shipping.
     */
    public function qualifiesForFreeShipping(int $subtotal): bool
    {
        return $subtotal >= $this->getFreeShippingThreshold();
    }
}
