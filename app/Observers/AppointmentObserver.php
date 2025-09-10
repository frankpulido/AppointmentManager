<?php
declare(strict_types=1);
namespace App\Observers;
// This model modifies the existing available slots for Treatment and Diagnose based on existing Appointments
use App\Models\Appointment;
use App\Models\AvailableTimeSlot;
use App\Models\AvailableTimeSlotDiagnosis;
use Carbon\Carbon;
use App\Jobs\RegenerateTreatmentSlotsJsonJob;
use App\Jobs\RegenerateDiagnosisSlotsJsonJob;
use Illuminate\Support\Facades\Log;
use Throwable;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        $practitionerId = $appointment->practitioner_id;
        $date = $appointment->appointment_date;
        
        // We add a 15' buffer to the starting and ending times :
        $buffer = Appointment::BUFFER_MINUTES;
        $startWithBuffer = Carbon::parse($appointment->appointment_start_time)->subMinutes($buffer);
        $endWithBuffer = Carbon::parse($appointment->appointment_end_time)->addMinutes($buffer);

        $models = [AvailableTimeSlot::class, AvailableTimeSlotDiagnosis::class];

        foreach ($models as $model) {
            try {
                $slots = $model::where('practitioner_id', $practitionerId)
                    ->where('slot_date', $date)
                    ->get();
                
                $slotsToDelete = $slots->filter(function ($slot) use ($startWithBuffer, $endWithBuffer) {
                    $slotStart = Carbon::parse($slot->slot_start_time);
                    $slotEnd = Carbon::parse($slot->slot_end_time);
                    return (
                        ($slotStart >= $startWithBuffer && $slotStart < $endWithBuffer) ||
                        ($slotEnd > $startWithBuffer && $slotEnd <= $endWithBuffer) ||
                        ($slotStart <= $startWithBuffer && $slotEnd >= $endWithBuffer) //||
                        //($slot->start_time >= $startWithBuffer && $slot->end_time <= $endWithBuffer)
                    );
                });

                $model::destroy($slotsToDelete->pluck('id')->all());
                
            } catch (Throwable $e) {
                Log::error('AppointmentObserver failed to remove overlapping slots: ' . $e->getMessage(), [
                    'appointment_id' => $appointment->id,
                    'model' => $model,
                ]);
            }
        }
        // Dispatch jobs to regenerate JSON files
        try {
            RegenerateTreatmentSlotsJsonJob::dispatch()->onQueue('json-generation');
            RegenerateDiagnosisSlotsJsonJob::dispatch()->onQueue('json-generation');
        } catch (Throwable $e) {
            Log::error('Failed to dispatch slot JSON regeneration jobs: ' . $e->getMessage(), [
                'appointment_id' => $appointment->id,
            ]);
        }
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "force deleted" event.
     */
    public function forceDeleted(Appointment $appointment): void
    {
        //
    }
}
