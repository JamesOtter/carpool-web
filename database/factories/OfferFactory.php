<?php

namespace Database\Factories;

use App\Models\Ride;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ride_id' => Ride::factory()->state(['ride_type' => 'offer']),
            'vehicle_number' => strtoupper(fake()->bothify('??## ??###')),
            'vehicle_model' => fake()->company() . ' ' . fake()->word(),
        ];
    }
}
