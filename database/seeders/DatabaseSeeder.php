<?php
declare(strict_types=1);
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
        
        User::updateOrCreate([
            'username' => 'frankpulido',
            'email' => 'frankpulido@me.com',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
            'practitioner_id' => 1, // assuming Frank is the first practitioner in practitioners table
        ]);

        User::updateOrCreate([
            'username' => 'janedoe',
            'email' => 'janedoe@practitioner.com',
            'password' => Hash::make('osteo1234'),
            'role' => 'practitioner',
            'practitioner_id' => 2, // assuming Jane is the second practitioner in practitioners table
        ]);

        User::updateOrCreate([
            'username' => 'johndoe',
            'email' => 'johndoe@superadmin.com',
            'password' => Hash::make('superadmin1234'),
            'role' => 'superadmin',
            'practitioner_id' => null,
        ]);
    }
}