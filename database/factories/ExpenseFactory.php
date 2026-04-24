<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'total_amount' => fake()->randomFloat(2, 0, 99999999.99),
            'installments' => fake()->numberBetween(-10000, 10000),
            'installment_amount' => fake()->randomFloat(2, 0, 99999999.99),
            'payment_plan' => fake()->word(),
            'category' => fake()->randomElement(["product_purchase","supplier_payment","operational_expense","others"]),
            'status' => fake()->randomElement(["pending","paid"]),
        ];
    }
}
