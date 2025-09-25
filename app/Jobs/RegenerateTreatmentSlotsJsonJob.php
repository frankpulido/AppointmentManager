<?php
declare(strict_types= 1);
namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Models\Practitioner;
use App\Models\AvailableTimeSlot;
use App\Services\SlotJsonDelivery\SlotJsonDeliveryStrategy;

class RegenerateTreatmentSlotsJsonJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Get ALL practitioners (no role filtering - public needs all)
            $practitioners = Practitioner::get()->mapWithKeys(function($p) {
                return [$p->id => $p->first_name . ' ' . $p->last_name];
            })->toArray();

            // Get ALL available 60-minute treatment slots (no practitioner filtering)
            $all_treatment_slots = AvailableTimeSlot::with('practitioner')->get();
 
            $available_treatment_slots_by_practitioner = $all_treatment_slots->filter(function ($slot) {
                $max_days_ahead = $slot->practitioner->getPractitionerSetting('max_days_ahead');
                return $slot->slot_date <= now()->addDays($max_days_ahead);
            })
            ->each(function ($slot) {
                $slot->makeHidden('practitioner');
            })
            ->groupBy('practitioner_id');

            // Build JSON structure matching AvailableSlotsController::index60()
            $jsonData = [
                'practitioners' => $practitioners,
                'available_slots_treatment' => $available_treatment_slots_by_practitioner->toArray(),
            ];

            // Get delivery strategy from config and deliver the JSON
            $strategy = app(SlotJsonDeliveryStrategy::class);
            $strategy->deliver('available_time_slots_treatment.json', $jsonData);

            Log::info('Treatment slots JSON file regenerated successfully');

        } catch (Throwable $e) {
            Log::error('Failed to regenerate treatment slots JSON file: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            
            // Re-throw to mark job as failed
            throw $e;
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('RegenerateTreatmentSlotsJsonJob failed permanently', [
            'exception' => $exception,
        ]);
    }
}