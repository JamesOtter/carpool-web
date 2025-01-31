<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Ride;
use Illuminate\Http\Request;

class RideController extends Controller
{
    public function index()
    {
        $rides = Ride::with('user', 'offer')->get(); //include related user and offer
        return view('rides.index', compact('rides'));
    }

    public function create()
    {
        return view('rides.create');
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'ride_type' => 'required|in:request,offer',
            'departure_address' => 'required|string|max:255',
            'departure_id' => 'required|string',
            'destination_address' => 'required|string|max:255',
            'destination_id' => 'required|string',
            'departure_datetime' => 'required|date',
            'number_of_passenger' => 'required|integer|min:1',
            'distance' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $ride = Ride::create($validatedData);

        // If the ride type is 'offer', create an entry in the 'offers' table.
        if ($ride->ride_type === 'offer') {
            $request->validate([
                'vehicle_number' => 'required|string|max:50',
                'vehicle_model' => 'required|string|max:50',
            ]);

            Offer::create([
                'ride_id' => $ride->id,
                'vehicle_number' => $request->vehicle_number,
                'vehicle_model' => $request->vehicle_model,
            ]);
        }

        return redirect()->route('rides.index')->with('success', 'Ride created successfully!');
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
}
