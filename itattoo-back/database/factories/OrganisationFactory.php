<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organisation>
 */
class OrganisationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $organisation_name = fake()->unique()->company();

        return [
            'name' => $organisation_name,
            'slug' => Str::slug($organisation_name),
            'owner_id' => 1,
        ];
    }
}
