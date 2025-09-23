<?php
declare(strict_types=1);
namespace App\Services;
// This Service creates new Appointments
use App\Models\Appointment;
use App\Models\Practitioner;
use App\Services\CheckAppointmentOverlapService;
use App\Exceptions\OverlapException;

class AppointmentCreationService
{
    public function create(array $newData)
    {
        // We check whether appointment_end_time is null or !exists and use defaults if so
        // Requests from public front end : !exists
        // Requests from admin back end : exists but null
        // The reason is that update() method set it null to allow changing kind_of_appointment
        $practitioner = Practitioner::find($newData['practitioner_id']);
        if (!array_key_exists('appointment_end_time', $newData) || is_null($newData['appointment_end_time'])) {
            $newData['appointment_end_time'] = $practitioner->calculateEndTime(
                $newData['kind_of_appointment'],
                $newData['appointment_start_time']
            );
        };
        
        // Check for new appointment overlap
        $overlapService = new CheckAppointmentOverlapService();
        if ($overlapService->checkOverlap(
            $newData['appointment_date'],
            $newData['appointment_start_time'],
            $newData['appointment_end_time'],
            $newData['practitioner_id']
        )) {
            throw new OverlapException('Esta hora de visita no está disponible en el sistema.');
        }

        $newAppointment = new Appointment($newData);
        $newAppointment->status = $newData['status'] ?? 'scheduled';
        $newAppointment->save();

        return $newAppointment;
    }
}