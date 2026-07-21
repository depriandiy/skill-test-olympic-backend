<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'department' => fake()->randomElement(['Technology', 'Human Resources', 'Finance', 'Operations']),
            'description' => fake()->paragraph(),
            'is_active' => true,
        ];
    }
}
