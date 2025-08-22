<?php
declare(strict_types=1);
namespace App\Services;
// This Service prevents Appointments created by the practitioner from overlapping (appointments requested by users are based on available free slots only)
use App\Models\Appointment;
use Carbon\Carbon;

class CheckAppointmentOverlapService
{
    /**
     * Check if there is any overlap between the given time frame and the existing appointments for the given practitioner.
     *
     * @param string $date      // Date in 'Y-m-d' format from request
     * @param string $start     // Time in 'H:i' format from request
     * @param string $end       // Time in 'H:i' format from request
     * @param int $practitionerId
     * @return boolean
     */
    public function checkOverlap(string $date, string $start, string $end, int $practitionerId) : bool
    {
        // We add a 15' buffer to the starting and ending times (we have to format since we receive data from form request) :
        $startWithBuffer = Carbon::parse($start)->subMinutes(15)->format('H:i:s');
        $endWithBuffer   = Carbon::parse($end)->addMinutes(15)->format('H:i:s');

        // We filter the existing appointments by practitioner and date
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
        /*
        $check3 = Appointment::where('practitioner_id', $practitionerId)
            ->whereDate('appointment_date', $date)
            ->where('appointment_start_time', '>=', $startWithBuffer)
            ->where('appointment_end_time', '<=', $endWithBuffer)
            ->exists();
        */
        return ($check1 || $check2);
        //return ($check1 || $check2 || $check3);
    }
}
?>