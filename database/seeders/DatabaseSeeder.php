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
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}