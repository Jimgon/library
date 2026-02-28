<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'grade_section' => fake()->randomElement(['7-A', '8-B', '9-C', '10-D', '11-ABM', '12-STEM']),
            'lrn' => fake()->unique()->numerify('##########'),
            'phone_number' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'email' => fake()->unique()->safeEmail(),
            'borrowed' => 0,
        ];
    }
}
