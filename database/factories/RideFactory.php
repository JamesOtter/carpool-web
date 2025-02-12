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
        $placeIds = [
            'ChIJ86uaP1cdyzERzg3kacAGzCg',
            'ChIJF734tvnjyjER4MAXatn3pAA',
            'ChIJAQAAkLviyjERDtY1CfyCY-c',
            'ChIJjYnWhJPiyjERwMpgP6cktj4',
            'ChIJo7ejzJPiyjERgzMZ6WDgR0g',
            'ChIJbWem0BXjyjERY8cTofWv-3I',
            'ChIJLcEKiJbiyjERLmscw4-KN2A',
            'ChIJdZ5flZbiyjERzwTKDyW6qGw',
            'ChIJw1bps_jjyjERcEpZ4yiwFbQ',
            'ChIJ2VcTSOrjyjERQCOMAp8bqAg',
            'ChIJAQAAcLziyjER_mmiV_iaBAk',
            'ChIJl8AzAgnjyjERxFNNglVafiY',
            'ChIJAfUV7b_iyjERpDseYdXYtp8',
            'ChIJp_MV7b_iyjERawEBkN1_J0s',
            'ChIJ1R3Ad8DiyjERPxFfNKhS5rM',
            'ChIJmdzXkcDiyjERCPcoCzoO7F0',
            'ChIJbxusaMDiyjER1i6t32KkbUo',
            'ChIJRRmb3MPiyjER5xYG5FMEdKQ',
            'ChIJoZ5gc8LiyjERTyOU05czBJI',
            'ChIJE4mJH-riyjERfO3olXV9iFM',
            'ChIJ5X7BTuriyjERetAAYQ6P8Ik',
            'ChIJzaNNeuviyjERdExEZMFhukU',
            'ChIJkZ6K98niyjERI7oQ5841dfY',
            'ChIJkZ6K98niyjERE8efnKu2GPw',
            'ChIJ_QkevbziyjERS0S8b-Djsss',
            'ChIJXz0-PrviyjERAMs5w2S9g3I',
            'ChIJY9I0c7viyjERoH-CSziuZ4o',
            'ChIJe8XSD7viyjERAMRUYx54024',
            'ChIJCzQLACTjyjER_KxqdGp8RTU',
            'ChIJvS2WE7ziyjERYJ5m3Xx-c0w',
            'ChIJNwx1r77iyjERcS_2X-M4j4g',
            'ChIJUU1NQK_jyjERtQk_q90oZr0',
            'ChIJHT_uWZfjyjERqNqHC76Zuec',
            'ChIJdRsr3K_iyjERglPsAM9saPE'
        ];

        return [
            'user_id' => User::factory(), // Generate a user and assign to the ride
            'ride_type' => fake()->randomElement(['request', 'offer']),
            'departure_address' => fake()->address(),
            'departure_id' => $departureId = fake()->randomElement($placeIds),
            'destination_address' => fake()->address(),
            'destination_id' => fake()->randomElement(array_diff($placeIds, [$departureId])),
            'departure_date' => fake()->dateTimeBetween('+1 days', '+1 week')->format('Y-m-d'),
            'departure_time' => fake()->time(),
            'number_of_passenger' => fake()->numberBetween(1, 4),
            'distance' => fake()->randomFloat(2, 1, 100),
            'duration' => fake()->numberBetween(1, 600),
            'price' => fake()->randomFloat(2, 10, 100),
            'description' => fake()->text(200),
            'status' => fake()->randomElement(['available', 'booked', 'expired'])
        ];
    }
}
