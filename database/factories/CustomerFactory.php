<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'cpf_cnpj' => fake()->word(),
            'birth_date' => fake()->date(),
            'phone' => fake()->phoneNumber(),
            'zip_code' => fake()->regexify('[A-Za-z0-9]{8}'),
            'street' => fake()->streetName(),
            'neighborhood' => fake()->word(),
            'number' => fake()->word(),
            'complement' => fake()->word(),
            'city' => fake()->city(),
            'state' => fake()->regexify('[A-Za-z0-9]{2}'),
            'is_active' => fake()->boolean(),
        ];
    }
}
