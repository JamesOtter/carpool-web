<?php

namespace App\Http\Controllers;

use App\Models\Ride;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Fetch Dialogflow Access Token dynamically
     */
    private function getDialogflowAccessToken()
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path(env('GOOGLE_APPLICATION_CREDENTIALS'))); // Path to your service account file
        $client->addScope('https://www.googleapis.com/auth/dialogflow');

        $token = $client->fetchAccessTokenWithAssertion();

        return $token['access_token'] ?? null;
    }

    /**
     * Send user input to Dialogflow and get response
     */
    public function chat(Request $request)
    {
        $userMessage = $request->input('message'); // User input from frontend
        $accessToken = $this->getDialogflowAccessToken();

        if (!$accessToken) {
            return response()->json(['error' => 'Failed to fetch Dialogflow token'], 500);
        }

        // Send request to Dialogflow
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json'
        ])->post("https://dialogflow.googleapis.com/v2/projects/carpool-web-449009/agent/sessions/123456:detectIntent", [
            'queryInput' => [
                'text' => [
                    'text' => $userMessage,
                    'languageCode' => 'en'
                ]
            ]
        ]);

        $dialogflowData = $response->json();
        $parameters = $dialogflowData['queryResult']['parameters'] ?? [];

        return $this->findRide($parameters);
    }
    public function findRide($parameters)
    {
        $departure = $parameters['departure_location'] ?? null;
        $destination = $parameters['destination_location'] ?? null;
        $dateTime = $parameters['departure_date'] ?? null;

        $date = date('Y-m-d', strtotime($dateTime));

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
            if ($ride->recurring_id) {
                $rideLink = route('recurring-rides.show', ['recurringRide' => $ride->recurring_id]);
            } else {
                $rideLink = route('rides.show', ['ride' => $ride->id]);
            }

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
