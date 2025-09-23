<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Exceptions\OverlapException;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAppointmentWebRequest;
use App\Models\Practitioner;
use App\Models\AvailableTimeSlot;
use App\Models\AvailableTimeSlotDiagnosis;
use App\Services\AppointmentCreationService;

class AppointmentController extends Controller
{
    public function create(Request $request)
    {
        // Logic to show form for creating a new appointment
    }

    public function store(StoreAppointmentWebRequest $request)
    {
        $validated = $request->validated();

        $slot = $this->findSlot($validated);
        // Check if the requested slot is available
        if (!$slot) {
            return response()->json(['error' => 'Esta hora de visita no esta disponible'], 400);
        }

        try {
            $creationService = new AppointmentCreationService();
            $appointment = $creationService->create($validated);
        } catch (OverlapException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json([
            'message' => 'Su cita ha sido reservada con éxito',
            'appointment' => $appointment->only([
                'kind_of_appointment',
                'appointment_date',
                'appointment_start_time',
                'appointment_end_time',
                'patient_first_name',
                'patient_last_name',
                'patient_email',
                'status',
            ])],
            201
        );
    }
    
    // Method to allow frontend reservation ONLY is the AvailableTimeSlot exists
    private function findSlot(array $validated)
    {
        $model = $validated['kind_of_appointment'] === 'diagnose'
            ? AvailableTimeSlotDiagnosis::class
            : AvailableTimeSlot::class;

        $practitioner = Practitioner::find($validated['practitioner_id']);
        $appointmentEndTime = $practitioner->calculateEndTime(
            $validated['kind_of_appointment'],
            $validated['appointment_start_time']
        );

        return $model::where('practitioner_id', $validated['practitioner_id'])
            ->where('slot_date', $validated['appointment_date'])
            ->where('slot_start_time', $validated['appointment_start_time'])
            ->where('slot_end_time', $appointmentEndTime)
            ->first();
    }
}