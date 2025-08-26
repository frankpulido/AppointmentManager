<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\AppointmentObserver;

#[ObservedBy([AppointmentObserver::class])]
class Appointment extends Model
{
    use HasFactory;
    public const BUFFER_MINUTES = 15;
    public const DURATION_MINUTES_DIAGNOSE = 90;
    public const DURATION_MINUTES_TREATMENT = 60;
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
    ];

    protected $casts = [
        'appointment_date' => 'date'
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
            set: fn($value) => preg_replace('/[\s\-]/', '', $value) // removes spaces and dashes
        );
    }

    public function practitioner()
    {
        return $this->belongsTo(Practitioner::class);
    }
}