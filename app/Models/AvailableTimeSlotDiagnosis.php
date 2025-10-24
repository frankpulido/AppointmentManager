<?php
declare(strict_types=1);
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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