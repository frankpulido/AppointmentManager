<?php
declare(strict_types=1);
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SpecialtySeeder::class,
            PractitionerSeeder::class,
            HolidaySeeder::class,
            VacationSeeder::class,
            AvailableTimeSlotSeeder::class,
            AvailableTimeSlotDiagnosisSeeder::class,
            AppointmentSeeder::class,
            WaitingListSeeder::class,
        ]);
        
        User::UpdateOrCreate([
            'username' => 'frankpulido',
            'email' => 'frankpulido@me.com',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
            'practitioner_id' => null,
        ]);

        User::UpdateOrCreate([
            'username' => 'lauradelasheras',
            'email' => 'laura@fisioterapiayosteopatiabarcelona.es',
            'password' => Hash::make('osteo1234'),
            'role' => 'practitioner',
            'practitioner_id' => 2, // assuming Laura is the second practitioner in practitioners table
        ]);
    }
}