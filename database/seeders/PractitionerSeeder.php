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
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'specialties' => ['osteopata', 'fisioterapeuta', 'fisioterapeuta deportivo'],
            'email' => 'janedoe@practitioner.com',
            'phone' => '677888999'
        ]);
    }
}