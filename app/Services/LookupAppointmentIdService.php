<?php
declare(strict_types=1);
namespace App\Services;
// This Service finds an Appointment an returns its ID
use App\Models\Appointment;
use Carbon\Carbon;

class LookupAppointmentIdService
{
    public function lookupAppointment(int $practitionerId, string $date, string $start, string $end, string $kindOfAppointment)
    {
        $appointment = Appointment::where('practitioner_id', $practitionerId)
            ->whereDate('appointment_date', $date)
            ->where('appointment_start_time', $start)
            ->where('appointment_end_time', $end)
            ->where('kind_of_appointment', $kindOfAppointment);
        return $appointment;
    }
}