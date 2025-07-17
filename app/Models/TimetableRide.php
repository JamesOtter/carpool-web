<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableRide extends Model
{
    protected $fillable = [
        'start_date',
        'end_date',
    ];

    public function rides()
    {
        return $this->hasMany(Ride::class, 'timetable_id');
    }
}
