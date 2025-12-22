<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'information' => $this->faker->sentence,
            'price' => $this->faker->numberBetween(1000, 10000), // In cents
            'stock' => $this->faker->numberBetween(0, 100),
            'image' => $this->faker->imageUrl(),
            'category_id' => Category::factory(),
            'store_id' => \App\Models\Store::factory(),
        ];
    }
}
