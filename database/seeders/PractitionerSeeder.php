<?php
declare(strict_types=1);
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Practitioner;

class PractitionerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Practitioner::create([
            'first_name' => 'Frank',
            'last_name' => 'Pulido',
            'specialties' => ['fisioterapeuta'],
            'email' => 'frankpulido@me.com',
            'phone' => '653343353'
        ]);
        Practitioner::create([
            'first_name' => 'Laura',
            'last_name' => 'de las Heras Cuesta',
            'specialties' => ['osteopata', 'fisioterapeuta', 'fisioterapeuta deportivo'],
            'email' => 'laura@fisioterapiayosteopatiabarcelona.es',
            'phone' => '677389143'
        ]);
    }
}