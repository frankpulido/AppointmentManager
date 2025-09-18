<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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