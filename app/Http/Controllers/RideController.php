<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Ride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RideController extends Controller
{
    public function index()
    {
        $rides = Ride::with('user', 'offer')->latest()->SimplePaginate(2); //include related user and offer
        return view('rides.index', compact('rides'));
    }

    public function create()
    {
        return view('rides.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'ride_type' => 'required|in:request,offer',
            'departure_address' => 'required|string|max:255',
            'departure_id' => 'required|string',
            'destination_address' => 'required|string|max:255',
            'destination_id' => 'required|string',
            'departure_date' => 'required|date',
            'departure_time' => 'required|date_format:H:i',
            'number_of_passenger' => 'required|integer|min:1',
            'distance' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            $ride = Ride::create([
                'user_id' => Auth::user()->id,
                'ride_type' => $request->ride_type,
                'departure_address' => $request->departure_address,
                'departure_id' => $request->departure_id,
                'destination_address' => $request->destination_address,
                'destination_id' => $request->destination_id,
                'departure_date' => $request->departure_date,
                'departure_time' => $request->departure_time,
                'number_of_passenger' => $request->number_of_passenger,
                'distance' => $request->distance,
                'duration' => $request->duration,
                'price' => $request->price,
                'description' => $request->description,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        // If the ride type is 'offer', create an entry in the 'offers' table.
        if ($ride->ride_type === 'offer') {
            $request->validate([
                'vehicle_number' => 'required|string|max:50',
                'vehicle_model' => 'required|string|max:50',
            ]);

            try {
                Offer::create([
                    'ride_id' => $ride->id,
                    'vehicle_number' => $request->vehicle_number,
                    'vehicle_model' => $request->vehicle_model,
                ]);

            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $ride = Ride::with('user', 'offer')->findOrFail($id);
        return view('rides.show', compact('ride'));
    }

    public function edit($id)
    {
        $ride = Ride::findOrFail($id);
        return view('rides.edit', compact('ride'));
    }

    public function update(Request $request, $id)
    {
        $ride = Ride::findOrFail($id);

        $validatedData = $request->validate([
            'departure_address' => 'nullable|string|max:255',
            'departure_latitude' => 'nullable|numeric',
            'departure_longitude' => 'nullable|numeric',
            'destination_address' => 'nullable|string|max:255',
            'destination_latitude' => 'nullable|numeric',
            'destination_longitude' => 'nullable|numeric',
            'departure_datetime' => 'nullable|date',
            'number_of_passenger' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $ride->update($validatedData);

        if ($ride->ride_type === 'offer' && $request->has(['vehicle_number', 'vehicle_model'])) {
            $offer = $ride->offer;
            $offer->update($request->only(['vehicle_number', 'vehicle_model']));
        }

        return redirect()->route('rides.index')->with('success', 'Ride updated successfully!');
    }

    public function destroy($id)
    {
        $ride = Ride::findOrFail($id);
        $ride->delete();

        return redirect()->route('rides.index')->with('success', 'Ride deleted successfully!');
    }

    public function getPrice(Request $request)
    {
        // Get ride ID or other parameters from the request
        $rideId = $request->input('ride_id');

        // Fetch the ride details from the database
        $ride = Ride::find($rideId);

        // Calculate dynamic price (e.g., base price + surge pricing)
        $basePrice = (float) $ride->price;
        $surgePrice = $this->calculateSurgePrice($ride); // Your logic for surge pricing
        $totalPrice = $basePrice + $surgePrice;

        // Return the price as a JSON response
        return response()->json([
            'total_price' => $totalPrice,
            'base_price' => $basePrice,
            'surge_price' => $surgePrice,
        ]);
    }

    private function calculateSurgePrice($ride)
    {
        // Example: Surge pricing during peak hours
        $currentHour = now()->hour;
        if ($currentHour >= 0 && $currentHour <= 24) { // Morning peak
            return $ride->price * 0.2; // 20% surge
        }
        return 0; // No surge
    }
}
