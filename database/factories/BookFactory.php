<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'author' => fake()->name(),
            'publisher' => fake()->company(),
            'isbn' => fake()->unique()->isbn13(),
            'category' => fake()->randomElement(['MATH', 'SCIENCE', 'FILIPINO', 'ENGLISH', 'MAPEH', 'HISTORY']),
            'copies' => fake()->numberBetween(1, 5),
            'available_copies' => 0,
            'status' => 'available',
        ];
    }
}
