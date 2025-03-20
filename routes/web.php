<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\RecurringRideController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\SessionController;
use App\Models\Booking;
use App\Models\Ride;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

//Rides
Route::get('/rides', [RideController::class, 'index'])
    ->name('rides.index');
Route::get('/rides/create', [RideController::class, 'create'])
    ->name('rides.create')
    ->middleware('auth');
Route::post('/rides', [RideController::class, 'store'])
    ->name('rides.store')
    ->middleware('auth');
Route::get('/rides/{ride}', [RideController::class, 'show'])
    ->name('rides.show');
Route::patch('/rides/{id}', [RideController::class, 'update'])
    ->name('rides.update')
    ->middleware('auth');
Route::delete('/rides/{id}', [RideController::class, 'destroy'])
    ->name('rides.destroy')
    ->middleware('auth');
Route::post('/get-price', [RideController::class, 'getPrice'])
    ->name('get.price');

//Recurring Ride
Route::get('/recurring-rides/{recurringRide}', [RecurringRideController::class, 'show'])
    ->name('recurring-rides.show');

//Dashboard
Route::get('/dashboard', function () {
    $userId = auth()->id();

    return view('dashboard', [
        'rides' => Ride::with('user', 'offer', 'recurringRide')
            ->where('user_id', $userId)
            ->get()
            ->groupBy(function ($ride) {
                return $ride->recurring_id ?? 'single_' . $ride->id;
            }),
        'incoming_bookings' => Booking::with('sender', 'receiver', 'ride.recurringRide')
            ->where('receiver_id', $userId)
            ->get()
            ->groupBy(function ($booking) {
                $groupKey = $booking->ride->recurring_id ?? 'single_' . $booking->ride_id;
                return $booking->status === 'declined' ? 'rejected_' . $groupKey : 'active_' . $groupKey;
            }),
        'outgoing_bookings' => Booking::with('sender', 'receiver', 'ride.recurringRide')
            ->where('sender_id', $userId)
            ->get()
            ->groupBy(function ($booking) {
                $groupKey = $booking->ride->recurring_id ?? 'single_' . $booking->ride_id;
                return $booking->status === 'declined' ? 'rejected_' . $groupKey : 'active_' . $groupKey;
            }),
    ]);
})->middleware('auth');

//Booking
Route::post('/bookings', [BookingController::class, 'store'])
    ->name('bookings.store')
    ->middleware('auth');
Route::patch('/bookings/{id}', [BookingController::class, 'update'])
    ->name('bookings.update')
    ->middleware('auth');
Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])
    ->name('bookings.destroy')
    ->middleware('auth');

//Login
Route::get('/login', [SessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');
Route::post('/login', [SessionController::class, 'store'])
    ->middleware('guest');
Route::get('/logout', [SessionController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

//Register
Route::get('/register', [RegisterController::class, 'create'])
    ->name('register')
    ->middleware('guest');
Route::post('/register', [RegisterController::class, 'store'])
    ->middleware('guest');

