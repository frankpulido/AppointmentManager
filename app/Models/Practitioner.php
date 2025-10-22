<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Practitioner extends Model
{
    use HasFactory;

    protected $table = 'practitioners';

    protected $fillable = [
        'first_name',
        'last_name',
        'specialties',
        'email',
        'phone',
        'custom_settings',
    ];

    protected $casts = [
        'first_name' => 'string',
        'last_name' => 'string',
        'specialties' => 'array',
        'email' => 'string',
        'phone' => 'string',
        'custom_settings' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function($practitioner) {
            if (empty($practitioner->custom_settings)) {
                $practitioner->custom_settings = [
                    'buffer_minutes' => Appointment::DEFAULT_BUFFER_MINUTES,
                    'duration_diagnosis' => Appointment::DEFAULT_DURATION_MINUTES_DIAGNOSE,
                    'duration_treatment' => Appointment::DEFAULT_DURATION_MINUTES_TREATMENT,
                    'max_days_ahead' => Appointment::DEFAULT_MAX_ONLINE_APPOINTMENTS_DAYS_AHEAD,
                    'treatment_slots' => AvailableTimeSlot::DEFAULT_TIME_SLOTS_TREATMENT,
                    'diagnosis_slots' => AvailableTimeSlotDiagnosis::DEFAULT_TIME_SLOTS_DIAGNOSIS,
                ];
            }
        });
    }

    public function getPractitionerSetting(string $key)
    {
        return $this->custom_settings[$key];
    }

    public function user()
    {
        return $this->hasOne(User::class, 'practitioner_id');
    }

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

    public function calculateEndTime(string $kind, string $startTime): string
    {
        $minutes = $kind === 'diagnose'
            ? $this->getPractitionerSetting('duration_diagnosis')
            : $this->getPractitionerSetting('duration_treatment');

        return Carbon::parse($startTime)
            ->addMinutes($minutes)
            ->format('H:i:s');
    }

    /*
    public function specialties()
    {
        return $this->belongsToMany(Specialty::class);
    }
    */
}