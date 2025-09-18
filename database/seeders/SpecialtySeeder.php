<?php
declare(strict_types=1);
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specialty;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            'fisioterapeuta',
            'osteopata',
            'fisioterapeuta deportivo'
        ];

        foreach ($specialties as $specialtyName) {
            Specialty::updateOrCreate(
                ['specialty_name' => $specialtyName],
                ['specialty_name' => $specialtyName]
            );
        }
    }
}