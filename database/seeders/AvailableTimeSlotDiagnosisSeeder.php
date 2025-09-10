<?php
declare(strict_types=1);
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\AvailableTimeSlotSeederService;
use App\Models\Practitioner;
use App\Jobs\RegenerateDiagnosisSlotsJsonJob;
use Illuminate\Support\Facades\Log;
use Throwable;

class AvailableTimeSlotDiagnosisSeeder extends Seeder
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
            $seederService->seedDiagnosis($practitioner_id, $startDate, $endDate); // Important : seedDiagnosis
        }

        // Dispatch job to regenerate JSON file after seeding
        try {
            RegenerateDiagnosisSlotsJsonJob::dispatch()->onQueue('json-generation');
        } catch (Throwable $e) {
            Log::error('Failed to dispatch treatment slots JSON regeneration job after seeding', [
                'error' => $e->getMessage()
            ]);
        }
    }
}