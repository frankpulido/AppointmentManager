<?php
declare(strict_types=1);
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\AvailableTimeSlotSeederService;
use App\Models\Practitioner;

class AvailableTimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $practitioners = Practitioner::pluck('id')->toArray();

        $startDate = now()->toDateString();               // 'Y-m-d' format
        $endDate = now()->addDays(180)->toDateString();   // 180 days ahead

        foreach ($practitioners as $practitioner_id) {
            $seederService = new AvailableTimeSlotSeederService();
            $seederService->seedTreatment($practitioner_id, $startDate, $endDate); // Important : seedTreatment
        }
    }
}