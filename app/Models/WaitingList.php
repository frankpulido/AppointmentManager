<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\WaitingList
 *
 * @property int $id
 * @property string $patient_first_name
 * @property string $patient_last_name
 * @property string $patient_email
 * @property string $patient_phone
 * @property int $practitioner_id
 * @property string $kind_of_appointment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingList query()
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingList whereKindOfAppointment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingList wherePatientEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingList wherePatientFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingList wherePatientLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingList wherePatientPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingList wherePractitionerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaitingList whereUpdatedAt($value)
 */
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
