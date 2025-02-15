<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\SessionController;
use App\Models\Ride;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    return view('dashboard', [
        'rides' => Ride::with('user', 'offer')
            ->where('user_id', auth()->id())
            ->latest()
            ->get()
    ]);
})->middleware('auth');

//Route::resource('rides', RideController::class);
Route::get('/rides', [RideController::class, 'index'])
    ->name('rides.index');
Route::get('/rides/create', [RideController::class, 'create'])
    ->name('rides.create')
    ->middleware('auth');
Route::post('/rides', [RideController::class, 'store'])
    ->name('rides.store')
    ->middleware('auth');
Route::get('/rides/{id}', [RideController::class, 'show'])
    ->name('rides.show');
//Route::get('/rides/{id}/edit', [RideController::class, 'edit'])->name('rides.edit');
Route::patch('/rides/{id}', [RideController::class, 'update'])
    ->name('rides.update')
    ->middleware('auth');
Route::delete('/rides/{id}', [RideController::class, 'destroy'])
    ->name('rides.destroy')
    ->middleware('auth');
Route::post('/get-price', [RideController::class, 'getPrice'])
    ->name('get.price');

Route::get('/login', [SessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');
Route::post('/login', [SessionController::class, 'store'])
    ->middleware('guest');
Route::get('/logout', [SessionController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

Route::get('/register', [RegisterController::class, 'create'])
    ->name('register')
    ->middleware('guest');
Route::post('/register', [RegisterController::class, 'store'])
    ->middleware('guest');

