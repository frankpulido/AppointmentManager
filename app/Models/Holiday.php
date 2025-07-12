<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Holiday extends Model
{
    use HasFactory;

    protected $table = 'holidays';

    protected $fillable = [
        'date',
        'name',
        'every_year',
    ];

    protected $casts = [
        'date' => 'date',
        'every_year' => 'boolean',
    ];
}
