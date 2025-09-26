<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\AppointmentObserver;
use Carbon\Carbon;

#[ObservedBy([AppointmentObserver::class])]
class Appointment extends Model
{
    use HasFactory;
    public const DEFAULT_BUFFER_MINUTES = 15;
    public const DEFAULT_DURATION_MINUTES_DIAGNOSE = 90;
    public const DEFAULT_DURATION_MINUTES_TREATMENT = 60;
    public const DEFAULT_MAX_ONLINE_APPOINTMENTS_DAYS_AHEAD = 91; # maximum days ahead for online appointments of 13 weeks
    public const VALID_STATUSES = ['scheduled', 're-scheduled', 'offered', 'cancelled', 'no-show'];
    public const VALID_KINDS = ['diagnose', 'treatment'];
    protected $table = 'appointments';
    protected $fillable = [
        'practitioner_id',
        'appointment_date',
        'appointment_start_time',
        'appointment_end_time',
        'patient_first_name',
        'patient_last_name',
        'patient_email',
        'patient_phone',
        'kind_of_appointment',
        'status',
        'on_line',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'on_line' => 'boolean',
    ];

    protected function patientFirstName(): Attribute
    {
        return Attribute::make(
            set: fn($value) => strtolower(trim($value))
        );
    }

    protected function patientLastName(): Attribute
    {
        return Attribute::make(
            set: fn($value) => strtolower(trim($value))
        );
    }

    protected function patientEmail(): Attribute
    {
        return Attribute::make(
            set: fn($value) => strtolower($value)
        );
    }

    protected function patientPhone(): Attribute
    {
        return Attribute::make(
            set: fn($value) => preg_replace('/(?!^\+)\D/', '', $value) // removes all non-digit chars but preserves "+" at the start
        );
    }

    /*
    protected function patientPhone(): Attribute
    {
        return Attribute::make(
            set: fn($value) => preg_replace('/[\s\-]/', '', $value) // removes spaces and dashes
        );
    }
    */

    // Method below moved to Practitioner Model
    // and modified to be non-static and use practitioner's setting
    // as duration may vary between practitioners
    // and also to avoid passing practitioner_id each time
    // and also to avoid querying Practitioner each time
    /*
    public function calculateEndTime(string $kind, string $startTime): string
    {
        $minutes = $kind === 'diagnose'
            ? $this->practitioner->getPractitionerSetting('duration_diagnosis')
            : $this->practitioner->getPractitionerSetting('duration_treatment');

        return Carbon::parse($startTime)
            ->addMinutes($minutes)
            ->format('H:i:s');
    }
    */

    public function practitioner()
    {
        return $this->belongsTo(Practitioner::class);
    }
}