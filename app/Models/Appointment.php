<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\AppointmentObserver;

#[ObservedBy([AppointmentObserver::class])]
class Appointment extends Model
{
    use HasFactory;
    public const VALID_STATUSES = ['scheduled', 'cancelled', 'no-show', 're-schedule'];
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
        'practitioner_id' => 'integer',
        'appointment_date' => 'date',
        'appointment_start_time' => 'datetime',
        'appointment_end_time' => 'datetime',
        'patient_first_name' => 'string',
        'patient_last_name' => 'string',
        'patient_email' => 'string',
        'patient_phone' => 'string',
        'kind_of_appointment' => 'string',
        'status' => 'string',
    ];
    public function practitioner()
    {
        return $this->belongsTo(Practitioner::class);
    }
}