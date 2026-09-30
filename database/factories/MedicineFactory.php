<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MedicineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word() . ' Medicine',
            'barcode' => fake()->ean13(),
            'unit_price' => fake()->randomFloat(2, 10, 500),
            'reorder_level' => fake()->numberBetween(5, 50),
        ];
    }
}
