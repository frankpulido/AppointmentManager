<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WaitingList extends Model
{
    use HasFactory;

    protected $table = 'waiting_lists';

    protected $fillable = [
        'patient_first_name',
        'patient_last_name',
        'patient_email',
        'patient_phone',
        'practitioner_id',
        'kind_of_appointment',
    ];

    protected $casts = [
        'kind_of_appointment' => 'string',
        //'kind_of_appointment' => 'enum:'.implode(',', Appointment::VALID_KINDS)
    ];
}
