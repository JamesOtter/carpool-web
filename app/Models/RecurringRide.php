<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringRide extends Model
{
    protected $fillable = [
        'recurrence_patter',
        'recurrence_days',
        'start_date',
        'end_date',
    ];

    public function rides()
    {
        return $this->hasMany(Ride::class, 'recurring_id');
    }
}
