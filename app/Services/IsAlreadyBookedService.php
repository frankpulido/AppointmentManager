<?php
declare(strict_types=1);
namespace App\Services;
// This Service prevents a client from booking an appointment if there is already one booked for the same practitioner within max_days_ahead.
use App\Models\Appointment;
use App\Models\Practitioner;
use Carbon\Carbon;

class IsAlreadyBookedService
{
    /**
     * Check if the user already has an appointment booked with the given practitioner within max_days_ahead.
     *
     * @param int $userId
     * @param int $practitionerId
     * @return boolean
     */
    public function isAlreadyBooked(array $validated) : bool
    {
        $practitioner = Practitioner::find($validated['practitioner_id']);
        $maxDaysAhead = $practitioner->getPractitionerSetting('max_days_ahead');
        $tomorrow = Carbon::now()->addDays(1)->startOfDay();
        $maxDate = Carbon::now()->addDays($maxDaysAhead)->endOfDay();

        return Appointment::where('patient_phone', $validated['patient_phone'])
            ->where('practitioner_id', $validated['practitioner_id'])
            ->whereBetween('appointment_date', [$tomorrow, $maxDate])
            ->whereIn('status', ['scheduled', 're-scheduled'])
            ->exists();
    }
}