<?php
declare(strict_types=1);
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\AvailableTimeSlot
 *
 * @property int $id
 * @property int $practitioner_id
 * @property \Illuminate\Support\Carbon $slot_date
 * @property string $slot_start_time
 * @property string $slot_end_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Practitioner $practitioner
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlot query()
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlot wherePractitionerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlot whereSlotDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlot whereSlotEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlot whereSlotStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AvailableTimeSlot whereUpdatedAt($value)
 */
class AvailableTimeSlot extends Model
{
    use HasFactory;
    
    public const DEFAULT_TIME_SLOTS_TREATMENT = [
        ['08:30:00', '09:30:00'],
        ['10:00:00', '11:00:00'],
        ['11:15:00', '12:15:00'],
        ['12:30:00', '13:30:00'],
        ['15:00:00', '16:00:00'],
        ['16:15:00', '17:15:00'],
        ['17:30:00', '18:30:00'],
        ['18:45:00', '19:45:00'],
    ];

    protected $table = 'available_time_slots';
    
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