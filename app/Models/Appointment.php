<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;
    public const VALID_STATUSES = ['scheduled', 'cancelled', 'no-show'];
    public const VALID_KINDS = ['diagnose', 'treatment'];
    protected $table = 'appointments';
    protected $fillable = [
        'patient_first_name',
        'patient_last_name',
        'patient_email',
        'patient_phone',
        'practitioner_id',
        'kind_of_appointment',
        'appointment_date',
        'appointment_start_time',
        'appointment_end_time',
        'status',
    ];
    protected $casts = [
        'status' => 'string',
        //'status' => 'enum:'.implode(',', self::VALID_STATUSES),
        'kind_of_appointment' => 'string',
        //'kind_of_appointment' => 'enum:'.implode(',', self::VALID_KINDS),
        'appointment_date' => 'date',
        'appointment_start_time' => 'datetime',
        'appointment_end_time' => 'datetime',
    ];
}
