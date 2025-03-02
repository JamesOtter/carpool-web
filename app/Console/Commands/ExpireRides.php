<?php

namespace App\Console\Commands;

use App\Models\Ride;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireRides extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
//    protected $signature = 'app:expire-bookings';
    protected $signature = 'rides:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set expired status for rides past departure date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $affected = Ride::where('status', '!=', 'expired')
            ->where('departure_date', '<', Carbon::now())
            ->update(['status' => 'expired']);

        $this->info("Expired $affected rides.");
    }
}
