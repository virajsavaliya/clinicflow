<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VisitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => null,
            'doctor_id' => null,
            'status' => 'waiting',
            'queue_number' => fake()->numberBetween(1, 50),
            'started_at' => null,
            'completed_at' => null,
            'notes' => null,
        ];
    }
}
