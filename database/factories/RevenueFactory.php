<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class RevenueFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'total_amount' => fake()->randomFloat(2, 0, 99999999.99),
            'installments' => fake()->numberBetween(-10000, 10000),
            'installment_amount' => fake()->randomFloat(2, 0, 99999999.99),
            'payment_plan' => fake()->word(),
            'status' => fake()->randomElement(["pending","paid"]),
        ];
    }
}
