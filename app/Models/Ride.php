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
        'departure_address',
        'departure_latitude',
        'departure_longitude',
        'destination_address',
        'destination_latitude',
        'destination_longitude',
        'departure_datetime',
        'number_of_passenger',
        'price',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offer()
    {
        return $this->hasOne(Offer::class);
    }
}
