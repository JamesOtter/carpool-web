<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\RecurringRide;
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
            'ride_id' => 'nullable|exists:rides,id',
            'recurring_id' => 'nullable|exists:recurring_rides,id',
            'receiver_id' => 'required',
        ]);

        // Ensure that at least one of ride_id or recurring_id is provided
        if (!$request->ride_id && !$request->recurring_id) {
            return response()->json(['success' => false, 'message' => 'Invalid booking request.']);
        }

        if ($request->ride_id) {
            // Handle Single Ride Booking
            return $this->createBooking($request->ride_id, $request->receiver_id);

        } elseif ($request->recurring_id) {
            // Handle Recurring Ride Booking
            $recurringRide = RecurringRide::with('rides')->find($request->recurring_id);

            if (!$recurringRide || $recurringRide->rides->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No rides found for this recurring ride.']);
            }

            foreach ($recurringRide->rides as $ride) {
                $response = $this->createBooking($ride->id, $request->receiver_id);
                if (!$response->original['success']) {
                    return $response; // Return error if any booking fails
                }
            }

            return response()->json(['success' => true, 'message' => 'All recurring rides booked successfully.']);
        }

//        //Prevent same user submit multiple same booking
//        $sameBooking = Booking::where('ride_id', $request->ride_id)
//                            ->where('status', '!=', 'declined')
//                            ->where('sender_id', Auth::id());
//
//        if($sameBooking->exists()){
//            return response()->json(['success' => false, 'message' => 'You already booked this ride. Check your dashboard.']);
//        }
//
//        //prevent user book their own ride
//        $ownRide = Ride::where('id', $request->ride_id)
//                        ->where('user_id', Auth::id());
//
//        if($ownRide->exists()){
//            return response()->json(['success' => false, 'message' => 'You cannot book your own ride.']);
//        }
//
//        //Create booking
//        try {
//            $booking = Booking::create([
//                'ride_id' => $request->ride_id,
//                'sender_id' => Auth::user()->id,
//                'receiver_id' => $request->receiver_id,
//            ]);
//
//        } catch (\Exception $e) {
//            return response()->json(['success' => false, 'message' => $e->getMessage()]);
//        }

        return response()->json(['success' => false, 'message' => 'Unexpected error occurred.']);
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
//        $booking = Booking::findOrFail($id);
//
//        if($request->action === 'accept'){
//
//            $booking->update([
//                'status' => 'accepted'
//            ]);
//
//            // Decline all other bookings with the same ride_id
//            $otherBookings = Booking::where('ride_id', $booking->ride_id)
//                                    ->where('id', '!=', $booking->id)
//                                    ->get();
//
//            foreach ($otherBookings as $otherBooking) {
//                $otherBooking->update(['status' => 'declined']);
//            }
//
//            //update ride status
//            $ride = Ride::findOrFail($booking->ride_id);
//            $ride->update(['status' => 'booked']);
//
//        }elseif($request->action === 'decline'){
//
//            $booking->update([
//                'status' => 'declined'
//            ]);
//        }

        $booking = Booking::findOrFail($id);
        $ride = Ride::findOrFail($booking->ride_id); // Get the ride linked to this booking

        // Check if it's a recurring ride
        if ($ride->recurring_id) {
            // Find all rides under the same recurring ride
            $rideIds = Ride::where('recurring_id', $ride->recurring_id)->pluck('id');

            // Find all related bookings for these rides
            $relatedBookings = Booking::whereIn('ride_id', $rideIds)->get();
        } else {
            // If it's a single ride, only process the current booking
            $relatedBookings = collect([$booking]);
            $rideIds = [$ride->id];
        }

        if ($request->action === 'accept') {
            // Accept the selected bookings
            foreach ($relatedBookings as $b) {
                $b->update(['status' => 'accepted']);
            }

            // Decline all other bookings for the same rides
            Booking::whereIn('ride_id', $rideIds)
                ->whereNotIn('id', $relatedBookings->pluck('id'))
                ->update(['status' => 'declined']);

            // Mark all rides as booked
            Ride::whereIn('id', $rideIds)->update(['status' => 'booked']);

        } elseif ($request->action === 'decline') {
            // Decline only the related bookings
            foreach ($relatedBookings as $b) {
                $b->update(['status' => 'declined']);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
//        $booking = Booking::findOrFail($id);
//
//        $booking->delete();

        $booking = Booking::findOrFail($id);

        // Find the ride associated with the booking
        $ride = Ride::findOrFail($booking->ride_id);

        if ($ride->recurring_id) {
            // find all bookings for rides in the same recurring series
            $relatedRideIds = Ride::where('recurring_id', $ride->recurring_id)->pluck('id');

            // Delete all bookings related to these rides
            Booking::whereIn('ride_id', $relatedRideIds)->delete();
        } else {
            // delete the booking
            $booking->delete();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Create a booking for a ride with necessary validations
     */
    private function createBooking($rideId, $receiverId)
    {
        // Prevent same user from submitting multiple same bookings
        $existingBooking = Booking::where('ride_id', $rideId)
            ->where('status', '!=', 'declined')
            ->where('sender_id', Auth::id())
            ->exists();

        if ($existingBooking) {
            return response()->json(['success' => false, 'message' => 'You already booked this ride. Check your dashboard.']);
        }

        // Prevent user from booking their own ride
        $ownRide = Ride::where('id', $rideId)
            ->where('user_id', Auth::id())
            ->exists();

        if ($ownRide) {
            return response()->json(['success' => false, 'message' => 'You cannot book your own ride.']);
        }

        // Create booking
        try {
            Booking::create([
                'ride_id' => $rideId,
                'sender_id' => Auth::user()->id,
                'receiver_id' => $receiverId,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
