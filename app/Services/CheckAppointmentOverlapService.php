<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\Appointment;
use Carbon\CarbonInterface;

class CheckAppointmentOverlapService
{
    /**
     * Check if there is any overlap between the given time frame and the existing appointments for the given practitioner.
     *
     * @param CarbonInterface $date
     * @param CarbonInterface $start
     * @param CarbonInterface $end
     * @param int $practitionerId
     * @return boolean
     */
    public function checkOverlap(CarbonInterface $date, CarbonInterface $start, CarbonInterface $end, $practitionerId)
    {
        $startWithBuffer = $start->copy()->subMinutes(15);
        $endWithBuffer   = $end->copy()->addMinutes(15);

        $check1 = Appointment::where('practitioner_id', $practitionerId)
            ->whereDate('appointment_date', $date)
            ->where(function ($query) use ($startWithBuffer, $endWithBuffer) {
                    $query->whereBetween('appointment_start_time', [$startWithBuffer, $endWithBuffer])
                    ->orWhereBetween('appointment_end_time', [$startWithBuffer, $endWithBuffer]);
            })
            ->exists();
        
        $check2 = Appointment::where('practitioner_id', $practitionerId)
            ->whereDate('appointment_date', $date)
            ->where('appointment_start_time', '<=', $startWithBuffer)
            ->where('appointment_end_time', '>=', $endWithBuffer)
            ->exists();
        
        $check3 = Appointment::where('practitioner_id', $practitionerId)
            ->whereDate('appointment_date', $date)
            ->where('appointment_start_time', '>=', $startWithBuffer)
            ->where('appointment_end_time', '<=', $endWithBuffer)
            ->exists();

        return ($check1 || $check2 || $check3);
    }
}
?>