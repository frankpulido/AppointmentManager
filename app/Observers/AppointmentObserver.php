<?php
declare(strict_types=1);
namespace App\Observers;

use App\Models\Appointment;
use App\Models\AvailableTimeSlot;
use App\Models\AvailableTimeSlotDiagnosis;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        $practitionerId = $appointment->practitioner_id;
        $date = $appointment->date;
        $start = $appointment->start_time;
        $end = $appointment->end_time;

        $models = [AvailableTimeSlot::class, AvailableTimeSlotDiagnosis::class];

        foreach ($models as $model) {
            $slots = $model::where('practitioner_id', $practitionerId)
                ->where('date', $date)
                ->get();

            $slotsToDelete = $slots->filter(function ($slot) use ($start, $end) {
                return (
                    ($slot->start_time >= $start && $slot->start_time < $end) ||
                    ($slot->end_time > $start && $slot->end_time <= $end) ||
                    ($slot->start_time <= $start && $slot->end_time >= $end)
                );
            });

            $model::destroy($slotsToDelete->pluck('id')->all());
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
