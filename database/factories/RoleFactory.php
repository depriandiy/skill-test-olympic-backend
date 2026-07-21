<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                Role::HR_ADMIN,
                Role::HR_STAFF,
                Role::INTERVIEWER,
                Role::CANDIDATE,
            ]),
        ];
    }
}
