<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\Preference;
use App\Models\Ride;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Preference::factory(10)->create()->each(function (Preference $preference) {
            $user = User::factory()->create(['preference_id' => $preference->id]);

            Ride::factory(5)->create(['user_id' => $user->id])->each(function ($ride){
                if($ride->ride_type === 'offer'){
                    Offer::factory()->create(['ride_id' => $ride->id]);
                }
            });
        });
    }
}
