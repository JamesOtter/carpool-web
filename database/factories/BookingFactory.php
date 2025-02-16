<?php

namespace Database\Factories;

use App\Models\Ride;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random ride
        $ride = Ride::inRandomOrder()->first();

        // Ensure sender and receiver are different users
        $sender = User::inRandomOrder()->first();
        do {
            $receiver = User::inRandomOrder()->first();
        } while ($receiver->id === $sender->id); // Avoid sender and receiver being the same

        return [
            'ride_id' => $ride ? $ride->id : Ride::factory(),
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => fake()->randomElement(['pending', 'accepted', 'rejected']),
        ];
    }
}
