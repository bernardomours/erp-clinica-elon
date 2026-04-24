<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductConsumptionFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(-10000, 10000),
            'consumption_date' => fake()->date(),
            'reason' => fake()->word(),
            'user_id' => User::factory(),
        ];
    }
}
