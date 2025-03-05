<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Ride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ride_id' => 'required',
            'receiver_id' => 'required',
        ]);

        //Prevent same user submit multiple same booking
        $sameBooking = Booking::where('ride_id', $request->ride_id)
                            ->where('status', '!=', 'declined')
                            ->where('sender_id', Auth::id());

        if($sameBooking->exists()){
            return response()->json(['success' => false, 'message' => 'You already booked this ride. Check your dashboard.']);
        }

        //prevent user book their own ride
        $ownRide = Ride::where('id', $request->ride_id)
                        ->where('user_id', Auth::id());

        if($ownRide->exists()){
            return response()->json(['success' => false, 'message' => 'You cannot book your own ride.']);
        }

        //Create booking
        try {
            $booking = Booking::create([
                'ride_id' => $request->ride_id,
                'sender_id' => Auth::user()->id,
                'receiver_id' => $request->receiver_id,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if($request->action === 'accept'){

            $booking->update([
                'status' => 'accepted'
            ]);

            // Decline all other bookings with the same ride_id
            $otherBookings = Booking::where('ride_id', $booking->ride_id)
                                    ->where('id', '!=', $booking->id)
                                    ->get();

            foreach ($otherBookings as $otherBooking) {
                $otherBooking->update(['status' => 'declined']);
            }

            //update ride status
            $ride = Ride::findOrFail($booking->ride_id);
            $ride->update(['status' => 'booked']);

        }elseif($request->action === 'decline'){

            $booking->update([
                'status' => 'declined'
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        $booking->delete();

        return response()->json(['success' => true]);
    }
}
