<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'ride_id',
        'vehicle_number',
        'vehicle_model'
    ];

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }
}
