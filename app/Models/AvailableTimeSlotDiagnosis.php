<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailableTimeSlotDiagnosis extends Model
{
    protected $table = 'available_time_slots_diagnosis';
    protected $fillable = [
        'practitioner_id',
        'date',
        'start_time',
        'end_time',
    ];
    protected $casts = [
        'practitioner_id' => 'integer',
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
    public function practitioner()
    {
        return $this->belongsTo(Practitioner::class);
    }
}