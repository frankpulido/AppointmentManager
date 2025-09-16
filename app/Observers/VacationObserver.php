<?php
declare(strict_types=1);
namespace App\Observers;

use App\Models\Vacation;
use App\Models\AvailableTimeSlot;
use App\Models\AvailableTimeSlotDiagnosis;
use App\Services\AvailableTimeSlotSeederService;
use App\Jobs\RegenerateTreatmentSlotsJsonJob;
use App\Jobs\RegenerateDiagnosisSlotsJsonJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class VacationObserver
{
    /**
     * Handle the Vacation "created" event.
     */
    public function created(Vacation $vacation): void
    {
        try {
            AvailableTimeSlot::where('practitioner_id', $vacation->practitioner_id)
                ->where('slot_date', '>=',  $vacation->start_date)
                ->where('slot_date', '<=', $vacation->end_date)
                ->delete();

            AvailableTimeSlotDiagnosis::where('practitioner_id', $vacation->practitioner_id)
                ->where('slot_date', '>=',  $vacation->start_date)
                ->where('slot_date', '<=', $vacation->end_date)
                ->delete();

        } catch (Throwable $e) {
            Log::error('Failed to delete available slots: ' . $e->getMessage(), [
                'practitioner_id' => $vacation->practitioner_id,
                'start_date' => $vacation->start_date,
                'end_date' => $vacation->end_date,
            ]);
        }

        try {
            RegenerateTreatmentSlotsJsonJob::dispatch()->onQueue('json-generation');
            RegenerateDiagnosisSlotsJsonJob::dispatch()->onQueue('json-generation');
            
        } catch (Throwable $e) {
            Log::error('Failed to dispatch slot JSON regeneration jobs: ' . $e->getMessage(), [
                'practitioner_id' => $vacation->practitioner_id,
            ]);
        }
    }

    /**
     * Handle the Vacation "updated" event.
     */
    public function updated(Vacation $vacation): void
    {
        //
    }

    /**
     * Handle the Vacation "deleted" event.
     */
    public function deleted(Vacation $vacation): void
    {
        // Regenerate slots for the vacation period
        $start_date = $vacation->start_date->toDateString();
        $end_date = $vacation->end_date->toDateString();
        try {
            $seederService = new AvailableTimeSlotSeederService();
            $seederService->seedTreatment($vacation->practitioner_id, $start_date, $end_date);
            $seederService->seedDiagnosis($vacation->practitioner_id, $start_date, $end_date);
        } catch (Throwable $e) {
            Log::error('Failed to regenerate available slots: ' . $e->getMessage(), [
                'practitioner_id' => $vacation->practitioner_id,
                'start_date' => $vacation->start_date,
                'end_date' => $vacation->end_date,
            ]);
        }
        
        // Dispatch jobs to regenerate JSON files
        try {
            RegenerateTreatmentSlotsJsonJob::dispatch()->onQueue('json-generation');
            RegenerateDiagnosisSlotsJsonJob::dispatch()->onQueue('json-generation');
        } catch (Throwable $e) {
            Log::error('Failed to dispatch slot JSON regeneration jobs: ' . $e->getMessage(), [
                'practitioner_id' => $vacation->practitioner_id,
            ]);
        }
    }

    /**
     * Handle the Vacation "restored" event.
     */
    public function restored(Vacation $vacation): void
    {
        //
    }

    /**
     * Handle the Vacation "force deleted" event.
     */
    public function forceDeleted(Vacation $vacation): void
    {
        //
    }
}