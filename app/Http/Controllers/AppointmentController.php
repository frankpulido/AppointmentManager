<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\AvailableTimeSlot;
use App\Models\AvailableTimeSlotDiagnosis;
use App\Services\CheckAppointmentOverlapService;
use Illuminate\Support\Facades\Log;
use Throwable;

class AppointmentController extends Controller
{
    public function create(Request $request)
    {
        // Logic to show form for creating a new appointment
    }

    public function store(StoreAppointmentRequest $request)
    {
        $validated = $request->validated();
        try {
            $slot = $this->findSlot($validated);

            if (!$slot) {
                return response()->json(['error' => 'Esta hora de visita no está disponible'], 400);
            }

            $appointment = new Appointment($validated);
            $appointment->status = 'scheduled';
            $appointment->save();

            return response()->json([
                'message' => 'Su cita ha sido reservada con éxito',
                'appointment' => $appointment
            ], 201);

        } catch (Throwable $e) {
            Log::error('Appointment store failed: ' . $e->getMessage(), [
                'request' => $validated,
            ]);

            return response()->json([
                'error' => 'Ocurrió un error al reservar la cita'
            ], 500);
        }
    }
    /*
    public function store(StoreAppointmentRequest $request)
    {
        $validated = $request->validated();
        $slot = $this->findSlot($validated);
        $overlapService = new CheckAppointmentOverlapService();

        // Check for appointment overlap (in case available slot was not removed by AppointmentObserver)
        if ($overlapService->checkOverlap(
            $validated['appointment_date'],
            $validated['appointment_start_time'],
            $validated['appointment_end_time'],
            $validated['practitioner_id']
        )) {
            return response()->json(['error' => 'Esta hora de visita no esta realmente disponible en el sistema. Agende otra hora o contacte directamente con el profesional de su elección para verificar disponibilidad'], 400);
        }

        // Check if the requested slot is available
        if (!$slot) {
            return response()->json(['error' => 'Esta hora de visita no esta disponible'], 400);
        } else {
            $appointment = new Appointment($validated);
            $appointment->status = 'scheduled';
            $appointment->save();

            return response()->json([
                'message' => 'Su cita ha sido reservada con éxito',
                'appointment' => $appointment],
                201
            );
        }        
    }
    */
    private function findSlot(array $validated)
    {
        $model = $validated['kind_of_appointment'] === 'diagnose'
            ? AvailableTimeSlotDiagnosis::class
            : AvailableTimeSlot::class;

        return $model::where('practitioner_id', $validated['practitioner_id'])
            ->where('slot_date', $validated['appointment_date'])
            ->where('slot_start_time', $validated['appointment_start_time'])
            ->where('slot_end_time', $validated['appointment_end_time'])
            ->first();
    }
}