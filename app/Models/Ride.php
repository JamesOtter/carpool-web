<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ride_type',
        'recurring_id',
        'timetable_id',
        'departure_address',
        'departure_id',
        'destination_address',
        'destination_id',
        'departure_date',
        'departure_time',
        'distance',
        'duration',
        'number_of_passenger',
        'price',
        'description',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offer()
    {
        return $this->hasOne(Offer::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function recurringRide()
    {
        return $this->belongsTo(RecurringRide::class, 'recurring_id');
    }

    public function timetableRide()
    {
        return $this->belongsTo(TimetableRide::class, 'timetable_id');
    }
}
