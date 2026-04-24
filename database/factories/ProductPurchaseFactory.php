<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductPurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'supplier_id' => Supplier::factory(),
            'quantity' => fake()->numberBetween(-10000, 10000),
            'total_cost' => fake()->randomFloat(2, 0, 99999999.99),
            'purchase_date' => fake()->date(),
            'user_id' => User::factory(),
        ];
    }
}
