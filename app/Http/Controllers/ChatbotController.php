<?php

namespace App\Http\Controllers;

use App\Models\Ride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function findRide(Request $request)
    {
        $departure = $request->input('queryResult.parameters.departure_location');
        $destination = $request->input('queryResult.parameters.destination_location');
        $date = $request->input('queryResult.parameters.departure_date');

        if (!$departure || !$destination || !$date) {
            return response()->json([
                'fulfillmentMessages' => [
                    ['text' => ['text' => ["I need the departure location, destination, and date to find a ride. Can you provide these details?"]]]
                ]
            ]);
        }

        // Find a matching ride
        $ride = Ride::where('departure_address', 'LIKE', "%$departure%")
            ->where('destination_address', 'LIKE', "%$destination%")
            ->where('departure_date', $date)
            ->where('status', 'active')
            ->first();

        if ($ride) {
            $rideLink = route('rides.show', ['id' => $ride->id]);

            return response()->json([
                'fulfillmentMessages' => [
                    ['text' => ['text' => ["Here is a ride for you: $rideLink"]]]
                ]
            ]);
        } else {
            return response()->json([
                'fulfillmentMessages' => [
                    ['text' => ['text' => ["Sorry, no rides found for your request."]]]
                ]
            ]);
        }
    }
}
