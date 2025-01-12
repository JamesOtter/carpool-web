<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home',[
        'locations' => [
            [
                'from_address' => '67, Laluan Tawas Sinaran',
                'to_address' => 'No.1, Pine Park',
                'time' => '10am',
                //add more details
            ],
            [
                'from_address' => 'Kampung Tawas Pasar Pagi',
                'to_address' => 'Taman Bercham Pertama',
                'time' => '8pm',
            ],
            [
                'from_address' => 'Restaurant Sangat Bagus',
                'to_address' => 'Tesco Extra Special',
                'time' => '1pm',
            ]
        ]
    ]);
});

Route::get('/about', function () {
    return view('about');
});

