<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\VacationObserver;

/**
 * App\Models\Vacation
 *
 * @property int $id
 * @property int $practitioner_id
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property string|null $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Practitioner $practitioner
 * @method static \Illuminate\Database\Eloquent\Builder|Vacation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vacation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vacation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Vacation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vacation whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vacation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vacation wherePractitionerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vacation whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vacation whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vacation whereUpdatedAt($value)
 */
#[ObservedBy([VacationObserver::class])]
class Vacation extends Model
{
    use HasFactory;
    protected $table = 'vacations';

    protected $fillable = [
        'practitioner_id',
        'start_date',
        'end_date',
        'reason',
    ];

    protected $casts = [
        'practitioner_id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'reason' => 'string',
    ];

    public function practitioner()
    {
        return $this->belongsTo(Practitioner::class);
    }
}
