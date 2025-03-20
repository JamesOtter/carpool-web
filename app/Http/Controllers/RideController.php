<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\RecurringRide;
use App\Models\Ride;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RideController extends Controller
{
    public function index(Request $request)
    {
        $rides = Ride::with('user', 'offer', 'recurringRide')
            ->where('status', 'active') // Filter only active rides
            ->latest();

        // Apply filters dynamically
        $rides->when($request->departure, fn ($query, $departure) => $query->where('departure_address', $departure))
            ->when($request->destination, fn ($query, $destination) => $query->where('destination_address', $destination))
            ->when($request->date, fn ($query, $date) => $query->whereDate('departure_date', $date))
            ->when($request->ride_type, fn ($query, $ride_type) => $query->where('ride_type', $ride_type))
            ->when($request->passengers, fn ($query, $passengers) => $query->where('number_of_passenger', '>=', $passengers));

        // Get all rides and group by recurring_id
        $rides = $rides->get()->groupBy('recurring_id');

        // Flatten the data and sort by the earliest date
        $sortedRides = collect();

        foreach ($rides as $recurring_id => $group) {
            if (!$recurring_id) {
                // Regular rides: Add them individually
                foreach ($group as $ride) {
                    $sortedRides->push([
                        'type' => 'regular',
                        'ride' => $ride,
                        'date' => $ride->departure_date,
                    ]);
                }
            } else {
                // Recurring rides: Only add once, use the earliest date (start_date)
                $sortedRides->push([
                    'type' => 'recurring',
                    'ride' => $group->first(), // Get the first ride in the group
                    'date' => $group->first()->recurringRide->start_date,
                ]);
            }
        }

        // Sort by date
        $sortedRides = $sortedRides->sortBy('date');

        return view('rides.index', compact('sortedRides'));
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
            'departure_date' => 'nullable|date',
            'departure_time' => 'required|date_format:H:i',
            'number_of_passenger' => 'required|integer|min:1',
            'distance' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            if($request->recurring_toggle){
                // Create the recurring ride entry
                $request->merge([
                    'recurrence_days' => is_string($request->recurrence_days)
                        ? explode(',', $request->recurrence_days)
                        : $request->recurrence_days
                ]);

                $request->validate([
                    'recurrence_pattern' => 'required|in:daily,weekly',
                    'recurrence_days' => 'nullable|array',
                    'recurrence_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                    'start_date' => 'required|date',
                    'end_date' => 'required|date',
                ]);

                $recurring = RecurringRide::create([
                    'recurrence_pattern' => $request->recurrence_pattern,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]);

                if($recurring->recurrence_pattern != 'daily'){
                    $recurring->update([
                       'recurrence_days' => $request->recurrence_days,
                    ]);
                }

                // Generate individual rides
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);
                $recurrenceDays = $request->recurrence_days ?? [];
                $rides = [];

                while ($startDate->lte($endDate)) {
                    if ($request->recurrence_pattern == 'daily' || in_array(strtolower($startDate->format('l')), $recurrenceDays)) {
                        $ride = Ride::create([
                            'user_id' => Auth::user()->id,
                            'ride_type' => $request->ride_type,
                            'recurring_id' => $recurring->id,
                            'departure_address' => $request->departure_address,
                            'departure_id' => $request->departure_id,
                            'destination_address' => $request->destination_address,
                            'destination_id' => $request->destination_id,
                            'departure_date' => $startDate->toDateString(),
                            'departure_time' => $request->departure_time,
                            'number_of_passenger' => $request->number_of_passenger,
                            'distance' => $request->distance,
                            'duration' => $request->duration,
                            'price' => $request->price,
                            'description' => $request->description,
                        ]);

                        $rides[] = $ride;
                    }

                    // Move to the next day
                    $startDate->addDay();
                }

            }else{
                // Non-recurring ride
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

                $rides = [$ride];
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        // Offer Creation for ride offer
        if ($request->ride_type === 'offer') {
            $request->validate([
                'vehicle_number' => 'required|string|max:50',
                'vehicle_model' => 'required|string|max:50',
            ]);

            try {
                foreach ($rides as $ride) {
                    Offer::create([
                        'ride_id' => $ride->id,
                        'vehicle_number' => $request->vehicle_number,
                        'vehicle_model' => $request->vehicle_model,
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function show(Ride $ride)
    {
        $ride->load(['user', 'offer']);

        return view('rides.show', compact('ride'));
    }

    public function edit($id)
    {
//        $ride = Ride::findOrFail($id);
//        return view('rides.edit', compact('ride'));
    }

    public function update(Request $request, $id)
    {
        $ride = Ride::findOrFail($id);

        // Determine if the ride is part of a recurring ride
        $isRecurring = $ride->recurring_id !== null;

        $inputData = [];
        foreach ($request->all() as $key => $value) {
            // Remove any numerical suffix from keys
            $cleanKey = preg_replace('/_\d+$/', '', $key);
            $inputData[$cleanKey] = $value;
        }

        // Validation rules
        $validationRules = [
            'ride_type' => 'required|in:request,offer',
            'departure_address' => 'required|string|max:255',
            'departure_id' => 'required|string',
            'destination_address' => 'required|string|max:255',
            'destination_id' => 'required|string',
            'departure_time' => 'required|date_format:H:i:s',
            'number_of_passenger' => 'required|integer|min:1',
            'distance' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ];

        // Only validate departure_date if it's a single ride (not recurring)
        if (!$isRecurring) {
            $validationRules['departure_date'] = 'required|date';
        }

        $validatedData = Validator::make($inputData, $validationRules);

        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'errors' => $validatedData->errors()], 422);
        }

        // If the ride is part of a recurring ride, update all rides with the same recurring_id
        if ($isRecurring) {
            Ride::where('recurring_id', $ride->recurring_id)
                ->update(collect($validatedData->validated())->except('departure_date')->toArray());
        } else {
            // Update only the selected ride
            $ride->update($validatedData->validated());
        }

        // Update vehicle details if it's an offer ride
        if ($ride->ride_type === 'offer' && isset($inputData['vehicle_number'], $inputData['vehicle_model'])) {
            Ride::where('recurring_id', $ride->recurring_id)->each(function ($ride) use ($inputData) {
                $ride->offer->update([
                    'vehicle_number' => $inputData['vehicle_number'],
                    'vehicle_model' => $inputData['vehicle_model'],
                ]);
            });
        }

//        $validatedData = Validator::make($inputData, [
//            'ride_type' => 'required|in:request,offer',
//            'departure_address' => 'required|string|max:255',
//            'departure_id' => 'required|string',
//            'destination_address' => 'required|string|max:255',
//            'destination_id' => 'required|string',
//            'departure_date' => 'required|date',
//            'departure_time' => 'required|date_format:H:i:s',
//            'number_of_passenger' => 'required|integer|min:1',
//            'distance' => 'required|numeric|min:0',
//            'duration' => 'required|integer|min:0',
//            'price' => 'required|numeric|min:0',
//            'description' => 'nullable|string',
//        ]);
//
//        if ($validatedData->fails()) {
//            return response()->json(['success' => false, 'errors' => $validatedData->errors()], 422);
//        }
//
//        $ride->update($validatedData->validated());
//
//        if ($ride->ride_type === 'offer' && isset($inputData['vehicle_number'], $inputData['vehicle_model'])) {
//            $offer = $ride->offer;
//            $offer->update([
//                'vehicle_number' => $inputData['vehicle_number'],
//                'vehicle_model' => $inputData['vehicle_model'],
//            ]);
//        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
//        $ride = Ride::findOrFail($id);
//
//        $ride->delete();
//
//        return response()->json(['success' => true]);

        $ride = Ride::findOrFail($id);

        try {
            if ($ride->recurring_id) {
                // Delete all rides in the recurring series
                Ride::where('recurring_id', $ride->recurring_id)->delete();
            } else {
                // Delete only the selected ride
                $ride->delete();
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete ride.', 'error' => $e->getMessage()], 500);
        }
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
