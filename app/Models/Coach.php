<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
    use HasFactory;

    protected $fillabe = [
        'id',
        'name',
        'timezone_id'
    ];

    public function coachSchedules()
    {
        return $this->hasMany(CoachSchedule::class);
    }
    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }
}
