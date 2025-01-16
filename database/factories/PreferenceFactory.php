<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Preference>
 */
class PreferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gender' => fake()->randomElement(['Male', 'Female', 'Other']),
            'smoking_habits' => fake()->boolean(),
            'race' => fake()->randomElement(['Malay', 'Chinese', 'Indian', 'Others']),
            'foods_and_drinks' => fake()->boolean(),
            'occupation' => fake()->jobTitle(),
        ];
    }
}
