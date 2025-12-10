<?php
declare(strict_types=1);
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\AvailableTimeSlotDiagnosis
 *
 * @property int $id
 * @property int $practitioner_id
 * @property \Illuminate\Support\Carbon $slot_date
 * @property string $slot_start_time
 * @property string $slot_end_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Practitioner $practitioner
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlotDiagnosis newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlotDiagnosis newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlotDiagnosis query()
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlotDiagnosis whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlotDiagnosis whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlotDiagnosis wherePractitionerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlotDiagnosis whereSlotDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlotDiagnosis whereSlotEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlotDiagnosis whereSlotStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlotDiagnosis whereUpdatedAt($value)
 */
class AvailableTimeSlotDiagnosis extends Model
{
    use HasFactory;
    
    public const DEFAULT_TIME_SLOTS_DIAGNOSIS = [
        ['08:30:00', '10:00:00'],
        ['10:30:00', '12:00:00'],
        ['12:30:00', '14:00:00'],
        ['15:00:00', '16:30:00'],
        ['17:00:00', '18:30:00'],
        ['19:00:00', '20:30:00'],
    ];

    protected $table = 'available_time_slots_diagnosis';
    
    protected $fillable = [
        'practitioner_id',
        'slot_date',
        'slot_start_time',
        'slot_end_time',
    ];

    protected $casts = [
        'slot_date' => 'date',
    ];

    public function practitioner()
    {
        return $this->belongsTo(Practitioner::class);
    }
}