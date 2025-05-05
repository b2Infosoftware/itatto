<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class CustomerFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone_number' => fake()->e164PhoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= 'iTattoo2024',
            'birth_date' => fake()->date(),
            'gender' => fake()->randomElement(['male', 'female', 'not_specified']),
            'is_minor' => '0',
            'country_id' => fake()->numberBetween(1, 195),
            'city' => fake()->city(),
            'address' => fake()->streetAddress(),
            'postal_code' => fake()->postcode(),
            'ssn' => fake()->ssn(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
