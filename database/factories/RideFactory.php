<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ride>
 */
class RideFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Generate a user and assign to the ride
            'ride_type' => fake()->randomElement(['request', 'offer']),
            'departure_address' => fake()->address(),
            'departure_latitude' => fake()->latitude(),
            'departure_longitude' => fake()->longitude(),
            'destination_address' => fake()->address(),
            'destination_latitude' => fake()->latitude(),
            'destination_longitude' => fake()->longitude(),
            'departure_datetime' => fake()->dateTimeBetween('+1 days', '+1 week'),
            'number_of_passenger' => fake()->numberBetween(1, 4),
            'price' => fake()->randomFloat(2, 10, 100),
            'description' => fake()->text(200),
        ];
    }
}
