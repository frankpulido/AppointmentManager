<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class AvailableTimeSlotDiagnosis extends Model
{
    use HasFactory;
    protected $table = 'available_time_slots_diagnosis';
    protected $fillable = [
        'practitioner_id',
        'date',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'date' => 'date',
        /*
        'start_time' => 'time',
        'end_time' => 'time',
        */
    ];

    /*
    protected function startTime() : Attribute
    {
        return Attribute::make(fn($value) => Carbon::parse($value)->format('H:i'));
    }

    protected function endTime() : Attribute
    {
        return Attribute::make(fn($value) => Carbon::parse($value)->format('H:i'));
    }
    */

    public function practitioner()
    {
        return $this->belongsTo(Practitioner::class);
    }
}