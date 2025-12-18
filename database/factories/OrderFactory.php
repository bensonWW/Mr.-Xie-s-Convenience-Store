<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Auto-create user if not provided
            'total_amount' => $this->faker->numberBetween(100, 10000),
            'status' => 'processing',
            'payment_method' => 'wallet',
            'logistics_number' => 'LOGI-' . date('Ymd') . '-' . mt_rand(10000, 99999),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
