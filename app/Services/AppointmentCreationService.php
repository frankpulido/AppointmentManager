<?php
declare(strict_types=1);
namespace App\Services;
// This Service creates new Appointments
use App\Models\Appointment;
use App\Services\CheckAppointmentOverlapService;
use Carbon\Carbon;
use Exception;

class AppointmentCreationService
{
    public function create(array $newData)
    {
        // We check whether appointment_end_time is null and use defaults if so
        if (is_null($newData['appointment_end_time'])) {
            if ($newData['kind_of_appointment'] === 'diagnose') {
                $slotDefaultEndTimeDiagnose = Carbon::parse($newData['appointment_start_time'])->addMinutes(Appointment::DURATION_MINUTES_DIAGNOSE)->format('H:i:s');
                $newData['appointment_end_time'] = $slotDefaultEndTimeDiagnose;
            } elseif ($newData['kind_of_appointment'] === 'treatment') {
                $slotDefaultEndTimeTreatment = Carbon::parse($newData['appointment_start_time'])->addMinutes(Appointment::DURATION_MINUTES_TREATMENT)->format('H:i:s');
                $newData['appointment_end_time'] = $slotDefaultEndTimeTreatment;
            } else {
                throw new \InvalidArgumentException('Tipo de cita no válido');
            }
        };
        
        // Check for new appointment overlap
        $overlapService = new CheckAppointmentOverlapService();
        if ($overlapService->checkOverlap(
            $newData['appointment_date'],
            $newData['appointment_start_time'],
            $newData['appointment_end_time'],
            $newData['practitioner_id']
        )) {
            throw new Exception('La fecha y hora de nueva cita se solapan con una cita existente');
        }

        $newAppointment = new Appointment($newData);
        $newAppointment->status = $newData['status'] ?? 'scheduled';
        $newAppointment->save();
        return $newAppointment;
    }
}