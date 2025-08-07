<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Practitioner extends Model
{
    use HasFactory;

    protected $table = 'practitioners';

    protected $fillable = [
        'first_name',
        'last_name',
        'specialization',
        'email',
        'phone',
    ];

    protected $casts = [
        'first_name' => 'string',
        'last_name' => 'string',
        'specialization' => 'string',
        'email' => 'string',
        'phone' => 'integer',
    ];
    public function availableTimeSlots()
    {
        return $this->hasMany(AvailableTimeSlot::class);
    }
    public function availableTimeSlotDiagnosis()
    {
        return $this->hasMany(AvailableTimeSlotDiagnosis::class);
    }
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
    public function vacations()
    {
        return $this->hasMany(Vacation::class);
    }
}
