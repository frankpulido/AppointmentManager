<?php
declare(strict_types=1);
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PractitionerSeeder::class,
            HolidaySeeder::class,
            VacationSeeder::class,
            AvailableTimeSlotSeeder::class,
            AvailableTimeSlotDiagnosisSeeder::class,
            AppointmentSeeder::class,
            WaitingListSeeder::class,
        ]);
        
        User::factory()->create([
            'name' => 'frankpulido',
            'email' => 'frankpulido@me.com',
            'password' => 'admin1234',
            'role' => 'admin',
            'practitioner_id' => null,
        ]);

        User::factory()->create([
            'name' => 'lauradelasheras',
            'email' => 'laura@fisioterapiayosteopatiabarcelona.es',
            'password' => 'osteo1234',
            'role' => 'practitioner',
            'practitioner_id' => 2, // assuming Laura is the second practitioner in practitioners table
        ]);
    }
}