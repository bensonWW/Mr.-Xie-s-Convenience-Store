<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'email' => $this->faker->companyEmail,
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
        ];
    }
}
