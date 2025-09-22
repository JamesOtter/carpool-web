<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\RecurringRide;
use App\Models\Ride;
use App\Models\TimetableRide;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TimetableRideController extends Controller
{
    public function index(){

    }

    public function create(){
        return view('timetableRides.create');
    }

    public function uploadTimetable(Request $request){
        $request->validate([
            'timetable' => 'required|image|mimes:jpg,jpeg,png|max:3072'
        ]);

        // Extract data from timetable
        try {
            $data = $this->extractTimetableData($request->file('timetable'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        if(empty($data)){
            return response()->json(['success' => false, 'message' => 'No data extracted.']);
        }

        // Generate rides based on extracted data
        $rides = $this->generateRidesFromTimetable($data);

        if(empty($rides)){
            return response()->json(['success' => false, 'message' => 'No rides found.']);
        }

        // Testing data (hard coded)
//        $data = [
//            [
//                "day" => "Mon",
//                "start_time" => "09:00",
//                "end_time" => "10:00",
//                "location" => "N003"
//            ],
//            [
//                "day" => "Mon",
//                "start_time" => "03:00",
//                "end_time" => "04:00",
//                "location" => "N001"
//            ],
//            [
//                "day" => "Mon",
//                "start_time" => "04:00",
//                "end_time" => "06:00",
//                "location" => "N112B(Lab)"
//            ],
//            [
//                "day" => "Tue",
//                "start_time" => "08:00",
//                "end_time" => "10:00",
//                "location" => "LDK2"
//            ],
//            [
//                "day" => "Wed",
//                "start_time" => "12:00",
//                "end_time" => "01:00",
//                "location" => "LDK2"
//            ],
//            [
//                "day" => "Wed",
//                "start_time" => "02:00",
//                "end_time" => "04:00",
//                "location" => "LDK1"
//            ],
//            [
//                "day" => "Wed",
//                "start_time" => "04:00",
//                "end_time" => "06:00",
//                "location" => "LDK2"
//            ],
//            [
//                "day" => "Fri",
//                "start_time" => "11:00",
//                "end_time" => "12:00",
//                "location" => "LDK1"
//            ]
//        ];

        // Create view to display rides
        $htmlBlocks = [];
        foreach ($rides as $index => $ride) {
            $htmlBlocks[] = view('components.ride-block', compact('ride', 'index'))->render();
        }

        return response()->json([
            'success' => true,
            'html_blocks' => $htmlBlocks,
            'message' => 'Image uploaded!',
        ]);
    }

    public function store(Request $request){
        // Validate static input
        $request->validate([
            'home_address' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'number_of_passenger' => 'required|integer|min:1',
            'price' => 'required|numeric|min:1',
            'vehicle_number' => 'required|string|max:50',
            'vehicle_model' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        // Validate dynamic input
        $index = 1;
        $rides = [];

        while ($request->has("day_$index")) {
            $request->validate([
                "day_$index" => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
                "departure_time_$index" => 'required|date_format:H:i',
                // At least one of destination or departure must exist
                "destination_address_$index" => 'nullable|string',
                "destination_id_$index" => 'nullable|string',
                "departure_address_$index" => 'nullable|string',
                "departure_id_$index" => 'nullable|string',
            ]);

            // Push the ride into an array for processing later
            $rides[] = [
                'day' => $request->input("day_$index"),
                'departure_time' => $request->input("departure_time_$index"),
                'departure_address' => $request->input("departure_address_$index"),
                'departure_id' => $request->input("departure_id_$index"),
                'destination_address' => $request->input("destination_address_$index"),
                'destination_id' => $request->input("destination_id_$index"),
            ];

            $index++;
        }

        if (empty($rides)) {
            return response()->json(['success' => false, 'message' => 'No rides found!']);
        }

        // Create Timetable Rides
        $timetableRide = TimetableRide::create([
            'start_date' => $request['start_date'],
            'end_date' => $request['end_date'],
        ]);

        // Create Recurring Rides
        foreach ($rides as $index => $ride) {
            $recurringRide = RecurringRide::create([
                'recurrence_pattern' => "weekly",
                'recurrence_days' => json_encode([strtolower($ride['day'])]),
                'start_date' => $request['start_date'],
                'end_date' => $request['end_date'],
            ]);

            // Then generate actual rides for this pattern
            $currentDate = Carbon::parse($request['start_date']);
            $endDate = Carbon::parse($request['end_date']);

            while ($currentDate->lte($endDate)) {
                if ($currentDate->format('l') === $ride['day']) {

                    // Calculate distance and duration
                    $distanceAndDuration = $this->getDistanceAndDuration($ride['departure_address'] ?? $request['home_address'], $ride['destination_address'] ?? $request['home_address']);
                    $distance = round(($distanceAndDuration['distance_value'] / 1000),2);
                    $duration = round($distanceAndDuration['duration_value'] / 60);

                    $singleRide = Ride::create([
                        'user_id' => auth()->id(),
                        'recurring_id' => $recurringRide->id,
                        'timetable_id' => $timetableRide->id,
                        'ride_type' => "offer",
                        'departure_address' => $ride['departure_address'] ?? $request['home_address'],
                        'departure_id' => $ride['departure_id'] ?? $request['home_id'],
                        'destination_address' => $ride['destination_address'] ?? $request['home_address'],
                        'destination_id' => $ride['destination_id'] ?? $request['home_id'],
                        'departure_date' => $currentDate->toDateString(),
                        'departure_time' => $ride['departure_time'],
                        'price' => $request->price,
                        'number_of_passenger' => $request->number_of_passenger,
                        'distance' => $distance,
                        'duration' => $duration,
                        'description' => $request->description,
                    ]);

                    Offer::create([
                        'ride_id' => $singleRide->id,
                        'vehicle_number' => $request->vehicle_number,
                        'vehicle_model' => $request->vehicle_model,
                    ]);
                }

                $currentDate->addDay();
            }
        }

        return response()->json(['success' => true]);
    }

    public function timetableSearch(Request $request){
        $request->validate([
            'timetable' => 'required|image|mimes:jpg,jpeg,png|max:3072'
        ]);

        // Extract data from timetable
        try {
            $data = $this->extractTimetableData($request->file('timetable'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        if(empty($data)){
            return response()->json(['success' => false, 'message' => 'No data extracted.']);
        }

        // Generate rides based on extracted data
        $rides = $this->generateRidesFromTimetable($data);

        if(empty($rides)){
            return response()->json(['success' => false, 'message' => 'No rides found.']);
        }

        session(['timetable_rides' => $rides]);

        return response()->json(['success' => true, 'redirect' => route('rides.index', ['use_timetable' => true])]);
    }

    private function to24Hour(string $time): string {
        $hour = intval(substr($time, 0, 2));

        // Morning time: 07:00–12:00 is AM (leave unchanged)
        if ($hour >= 7 && $hour <= 12) {
            return $time;
        }

        // Afternoon: 01:00–06:00 means 13:00–18:00
        if ($hour >= 1 && $hour <= 6) {
            $hour += 12;
            return str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . substr($time, 3);
        }

        return $time;
    }

    private function getDistanceAndDuration($origin, $destination)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');

        $response = Http::get("https://maps.googleapis.com/maps/api/distancematrix/json", [
            'origins' => $origin,
            'destinations' => $destination,
            'key' => $apiKey,
            'units' => 'metric',
        ]);

        if (!$response->successful()) {
            return $response->status();
        }

        $data = $response->json();

        if ($data['status'] !== 'OK') {
            return 'API error: ' . $data['status'] . ' url: ' . $response->effectiveUri();
        }

        $elementStatus = $data['rows'][0]['elements'][0]['status'];

        if ($elementStatus !== 'OK') {
            return 'Route not found: ' . $elementStatus;
        }

        if ($data['status'] === 'OK' && $data['rows'][0]['elements'][0]['status'] === 'OK') {
            $element = $data['rows'][0]['elements'][0];

            return [
                'distance_text' => $element['distance']['text'],   // e.g., "5.2 km"
                'distance_value' => $element['distance']['value'], // in meters
                'duration_text' => $element['duration']['text'],   // e.g., "12 mins"
                'duration_value' => $element['duration']['value'], // in seconds
            ];
        }

        return null;
    }

    private function extractTimetableData($uploadedFile)
    {
        $response = Http::attach(
            'file', // Must match FastAPI param name
            file_get_contents($uploadedFile->getRealPath()),
            $uploadedFile->getClientOriginalName()
        )->post('http://ocrtimetable006.bagefchkhcgcg4ef.southeastasia.azurecontainer.io/ocr-timetable');

        if ($response->failed()) {
            throw new \Exception("OCR failed: " . $response->status() . ' ' . $response->body());
        }

        return $response->json()['data'] ?? [];
    }

    private function generateRidesFromTimetable(array $data): array
    {
        // Pre-create rides from data
        $dayMap = [
            'mon' => 'Monday',
            'tue' => 'Tuesday',
            'wed' => 'Wednesday',
            'thu' => 'Thursday',
            'fri' => 'Friday',
            'sat' => 'Saturday',
            'sun' => 'Sunday',
        ];
        $locationMap = [
            'B' => [
                'name' => 'Block B Learning Complex 1',
                'address' => 'Learning Complex 1, 31900 Kampar, Perak, Malaysia',
                'id' => 'ChIJbxusaMDiyjER1i6t32KkbUo'
            ],
            'D' => [
                'name' => 'Block D Faculty of Science (FSc)',
                'address' => 'Jalan Universiti, Bandar Barat, Jalan Perdana, 31900 Kampar, Perak, Malaysia',
                'id' => 'ChIJt_UJxsPiyjERfsrlxPeIiYE'
            ],
            'E' => [
                'name' => 'Block E Faculty of Engineering and Green Technology (FEGT)',
                'address' => 'Jalan Universiti, Bandar Barat, 31900 Kampar, Perak, Malaysia',
                'id' => 'ChIJg1wLPMHiyjER5uOmhqva0vg'
            ],
            'H' => [
                'name' => 'Block H Faculty of Business and Finance (FBF)',
                'address' => 'Faculty of Business And Finance (FBF), 31900 Malim Nawar, Perak, Malaysia',
                'id' => 'ChIJRRmb3MPiyjER5xYG5FMEdKQ'
            ],
            'I' => [
                'name' => 'Block I Lecture Complex I',
                'address' => 'Lecture Complex I, 31900 Malim Nawar, Perak, Malaysia',
                'id' => 'ChIJWTBJ2cPiyjERb9_69LB-yV4'
            ],
            'L' => [
                'name' => 'Block L Learning Complex II',
                'address' => 'Lecture Complex II, Jalan Universiti, Bandar Barat, 31900 Malim Nawar, Perak, Malaysia',
                'id' => 'ChIJVVWVbMLiyjERizvApllA-w4'
            ],
            'N' => [
                'name' => 'Block N Faculty of Information and Communication Technology (FICT)',
                'address' => 'Faculty of Information and Communication Technology (FICT), 31900 Kampar, Perak, Malaysia',
                'id' => 'ChIJ5X7BTuriyjERetAAYQ6P8Ik'
            ],
            'P' => [
                'name' => 'Block P Institute of Chinese Studies (ICS)',
                'address' => 'Jalan Universiti, Bandar Barat, 31900 Kampar, Perak, Malaysia',
                'id' => 'ChIJ29qZyMPiyjERcxO7w9AYdxA'
            ],
        ];
        $rides = [];
        $grouped = collect($data)->groupBy('day');

        foreach ($grouped as $day => $classes) {
            $sorted = collect($classes)->sortBy(function ($class) {
                return strtotime($this->to24Hour($class['start_time']));
            })->values();

            for ($i = 0; $i < $sorted->count(); $i++) {
                $class = $sorted[$i];
                $start = Carbon::createFromFormat('H:i', $this->to24Hour($class['start_time']));
                $end = Carbon::createFromFormat('H:i', $this->to24Hour($class['end_time']));

                // --- Before class check ---
                $hasConflictBefore = $sorted->some(function ($other) use ($class, $start) {
                    if ($other === $class) return false;
                    $otherEnd = Carbon::createFromFormat('H:i', $this->to24Hour($other['end_time']));
                    return $otherEnd->gt($start->copy()->subHours(2)) && $otherEnd->lte($start);
                });

                if (!$hasConflictBefore) {
                    $prefix = strtoupper(substr($class['location'], 0, 1));
                    $mapped = $locationMap[$prefix] ?? ['name' => null, 'address' => null, 'id' => null];

                    $rides[] = [
                        "day" => $dayMap[strtolower($day)] ?? ucfirst($day),
                        "departure_time" => $start->copy()->subMinutes(30)->format('H:i'),
                        "departure" => null,
                        "destination" => $mapped['name'],
                        "destination_address" => $mapped['address'],
                        "destination_id" => $mapped['id']
                    ];
                }

                // --- After class check ---
                $hasConflictAfter = $sorted->some(function ($other) use ($class, $end) {
                    if ($other === $class) return false;
                    $otherStart = Carbon::createFromFormat('H:i', $this->to24Hour($other['start_time']));
                    return $otherStart->lt($end->copy()->addHours(2)) && $otherStart->gte($end);
                });

                if (!$hasConflictAfter) {
                    $prefix = strtoupper(substr($class['location'], 0, 1));
                    $mapped = $locationMap[$prefix] ?? ['name' => null, 'address' => null, 'id' => null];

                    $rides[] = [
                        "day" => $dayMap[strtolower($day)] ?? ucfirst($day),
                        "departure_time" => $end->format('H:i'),
                        "departure" => $mapped['name'],
                        "destination" => null,
                        "departure_address" => $mapped['address'],
                        "departure_id" => $mapped['id'],
                    ];
                }
            }
        }

        return $rides;
    }
}
