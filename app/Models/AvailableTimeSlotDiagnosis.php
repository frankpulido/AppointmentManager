<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailableTimeSlotDiagnosis extends AvailableTimeSlot
{
    protected $table = 'available_time_slots_diagnosis';
    protected $fillable = [
        'date',
        'start_time',
        'end_time',
    ];
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
}
