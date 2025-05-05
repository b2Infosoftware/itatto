<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
            'duration' => fake()->randomElement([15, 30, 45, 60, 75, 90]),
            'price' => rand(20, 100),
            'description' => fake()->sentence(),
            'color' => fake()->hexColor(),
            'duration' => fake()->randomElement([15, 30, 45, 60]),
            'is_private' => fake()->randomElement([0, 1]),
            'hide_from_statistics' => fake()->randomElement([0, 1]),
            'is_hourly_rated' => fake()->randomElement([0, 1]),
            'organisation_id' => 1,
            'category_id' => fake()->randomElement([1, 2]),
        ];
    }
}
