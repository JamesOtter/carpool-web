<?php

namespace App\Http\Controllers;

use App\Models\RecurringRide;
use Illuminate\Http\Request;

class RecurringRideController extends Controller
{
    public function show(RecurringRide $recurringRide)
    {
        $recurringRide->load('rides.user');

        $firstRide = $recurringRide->rides->first();

        return view('recurringRides.show', compact('recurringRide', 'firstRide'));
    }
}
