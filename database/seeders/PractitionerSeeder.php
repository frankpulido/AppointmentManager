<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            'first_name' => 'Laura',
            'last_name' => 'de las Heras Cuesta',
            'specialization' => 'Osteópata y Fisioterapeuta Deportiva',
            'email' => 'laura@fisioterapiayosteopatiabarcelona.es',
            'phone' => '677389143'
        ]);

        Practitioner::create([
            'first_name' => 'Frank',
            'last_name' => 'Pulido',
            'specialization' => 'Osteópata y Fisioterapeuta Deportivo',
            'email' => 'frank@fisioterapiayosteopatiabarcelona.es',
            'phone' => '653343353'
        ]);
    }
}
