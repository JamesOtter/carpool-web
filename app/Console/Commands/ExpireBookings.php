<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set expired status for booking past departure date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now(); // Get the current time

        // Find bookings that are NOT expired and where the ride's departure_date has passed
        $affected = Booking::where('status', '!=', 'expired')
                ->whereHas('ride', function ($query) use ($now) {
                    $query->where('departure_date', '<', $now);
                })
                ->update(['status' => 'expired']);

        $this->info("Expired $affected bookings.");
    }
}
