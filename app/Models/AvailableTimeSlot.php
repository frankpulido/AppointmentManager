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
        'slot_date',
        'slot_start_time',
        'slot_end_time'
    ];

    protected $casts = [
        'slot_date' => 'date',
    ];

    public function practitioner()
    {
        return $this->belongsTo(Practitioner::class);
    }
}