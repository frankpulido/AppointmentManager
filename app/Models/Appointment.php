<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\AppointmentObserver;
/**
 * App\Models\Appointment
 * @property int $id
 * @property int $practitioner_id
 * @property \Illuminate\Support\Carbon $appointment_date
 * @property string $appointment_start_time
 * @property string $appointment_end_time
 * @property string $patient_first_name
 * @property string $patient_last_name
 * @property string $patient_email
 * @property string $patient_phone
 * @property string $kind_of_appointment
 * @property string $status
 * @property bool $on_line
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Practitioner $practitioner
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereAppointmentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereAppointmentEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereAppointmentStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereKindOfAppointment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereOnLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment wherePatientEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment wherePatientFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment wherePatientLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment wherePatientPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment wherePractitionerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereUpdatedAt($value)
 */
#[ObservedBy([AppointmentObserver::class])]
class Appointment extends Model
{
    use HasFactory;
    public const DEFAULT_BUFFER_MINUTES = 15;
    public const DEFAULT_DURATION_MINUTES_DIAGNOSE = 90;
    public const DEFAULT_DURATION_MINUTES_TREATMENT = 60;
    public const DEFAULT_PRICE_EUROS_DIAGNOSE = 75;
    public const DEFAULT_PRICE_EUROS_TREATMENT = 65;
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

    public function practitioner()
    {
        return $this->belongsTo(Practitioner::class);
    }
}