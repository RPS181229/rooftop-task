<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachSchedule extends Model
{
    use HasFactory;

    protected $fillabe = [
        'name',
        'timezone',
        'day_of_week',
        'day_of_week',
        'available_at',
        'available_until'
    ];
}
