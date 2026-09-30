<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'age' => fake()->numberBetween(18, 80),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'address' => fake()->address(),
        ];
    }
}
