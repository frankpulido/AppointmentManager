<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Specialty
 *
 * @property int $id
 * @property string $specialty_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty query()
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereSpecialtyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereUpdatedAt($value)
 */
class Specialty extends Model
{
    use HasFactory;
    protected $table = 'specialties';

    protected $fillable = [
        'specialty_name',
    ];

    protected $casts = [
        'specialty_name' => 'string',
    ];

    /*
    public function practitioners()
    {
        return $this->belongsToMany(Practitioner::class);
    }
    */
}