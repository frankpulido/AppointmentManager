<?php
declare(strict_types=1);
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'slot_end_time'
    ];

    protected $casts = [
        'slot_date' => 'date',
    ];

    public static function calculatedEndTime(string $startTime): string
    {
        $minutes = Appointment::DURATION_MINUTES_TREATMENT;
        return Carbon::parse($startTime)
            ->addMinutes($minutes)
            ->format('H:i:s');
    }

    public function practitioner()
    {
        return $this->belongsTo(Practitioner::class);
    }
}