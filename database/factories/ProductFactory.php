<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'quantity' => fake()->numberBetween(-10000, 10000),
            'batch' => fake()->word(),
            'expiration_date' => fake()->date(),
            'unit_cost' => fake()->randomFloat(2, 0, 99999999.99),
        ];
    }
}
