<?php
declare(strict_types=1);
namespace App\Observers;
// This model modifies the existing available slots for Treatment and Diagnose based on existing Appointments
use App\Models\Appointment;
use App\Models\AvailableTimeSlot;
use App\Models\AvailableTimeSlotDiagnosis;
use Carbon\Carbon;
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
        $startWithBuffer = Carbon::parse($appointment->appointment_start_time)->subMinutes(15);
        $endWithBuffer = Carbon::parse($appointment->appointment_end_time)->addMinutes(15);
        /*
        $startWithBuffer = Carbon::parse($appointment->appointment_start_time)->subMinutes(15)->format('H:i:s');
        $endWithBuffer = Carbon::parse($appointment->appointment_end_time)->addMinutes(15)->format('H:i:s');
        */

        $models = [AvailableTimeSlot::class, AvailableTimeSlotDiagnosis::class];

        foreach ($models as $model) {
            try {
                $slots = $model::where('practitioner_id', $practitionerId)
                    ->where('date', $date)
                    ->get();
                
                $slotsToDelete = $slots->filter(function ($slot) use ($startWithBuffer, $endWithBuffer) {
                    $slotStart = Carbon::parse($slot->start_time);
                    $slotEnd = Carbon::parse($slot->end_time);
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
