<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\AvailableTimeSlot;
use App\Models\AvailableTimeSlotDiagnosis;

class AppointmentController extends Controller
{
   public function create(Request $request)
    {
        // Logic to show form for creating a new appointment
    }

    public function store(StoreAppointmentRequest $request)
    {
        $validated = $request->validated();
        $slot = $this->findSlot($validated);

        if (!$slot) {
            return response()->json(['error' => 'Esta hora de visita no esta disponible'], 400);
        } else {
            /*
            $appointment = Appointment::create([
                ...$validated,
                'status' => 'scheduled',
            ]);
            */
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
    private function findSlot(array $validated)
    {
        $model = $validated['kind_of_appointment'] === 'diagnose'
            ? AvailableTimeSlotDiagnosis::class
            : AvailableTimeSlot::class;

        return $model::where('practitioner_id', $validated['practitioner_id'])
            ->where('date', $validated['appointment_date'])
            ->where('start_time', $validated['appointment_start_time'])
            ->where('end_time', $validated['appointment_end_time'])
            ->first();
    }
}