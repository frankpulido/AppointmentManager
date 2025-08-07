<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AvailableTimeSlot extends Model
{
    use HasFactory;
    protected $table = 'available_time_slots';
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