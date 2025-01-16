<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    use HasFactory;

    protected $fillable = [
        'gender',
        'smoking_habits',
        'race',
        'foods_and_drinks',
        'occupation',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
