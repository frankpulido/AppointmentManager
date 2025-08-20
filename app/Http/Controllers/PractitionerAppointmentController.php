<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Http\Requests\StoreAppointmentRequest;
use App\Services\CheckAppointmentOverlapService;
use Illuminate\Contracts\Cache\Store;

class PractitionerAppointmentController extends Controller
{
    public function index()
    {
        // Logic to display appointments
        $appointments = Appointment::all();
        return response()->json($appointments, 200);
    }

    public function create(Request $request)
    {
        // Logic to show form for creating a new appointment
    }

    public function store(StoreAppointmentRequest $request)
    {
        $validated = $request->validated();
        $overlapService = new CheckAppointmentOverlapService();
        // Check for appointment overlap
        if ($overlapService->checkOverlap(
            $validated['appointment_date'],
            $validated['appointment_start_time'],
            $validated['appointment_end_time'],
            $validated['practitioner_id']
        )) {
            return response()->json(['error' => 'La fecha y hora de la cita se solapan con una cita existente'], 400);
        }

        $appointment = new Appointment($validated);
        $appointment->status = 'scheduled';
        $appointment->save();

        return response()->json([
            'message' => 'La visita ha sido reservada con éxito',
            'appointment' => $appointment
        ], 201);
    }

    public function show($id)
    {
        // Logic to display a specific appointment
    }

    public function edit($id)
    {
        // Logic to show form for editing an existing appointment
    }

    public function update(Request $request, $id)
    {
        // Logic to update an existing appointment
    }

    public function destroy($id)
    {
        // Logic to delete an appointment
    }

    public function search(Request $request)
    {
        // Logic to search for appointments based on criteria
    }

    public function filterByDate(Request $request)
    {
        // Logic to filter appointments by date
    }

    public function filterByPractitioner(Request $request)
    {
        // Logic to filter appointments by practitioner
    }

    public function filterByStatus(Request $request)
    {
        // Logic to filter appointments by status
    }

    public function reschedule(Request $request, $id)
    {
        // Logic to reschedule an appointment
    }

    public function cancel($id)
    {
        // Logic to cancel an appointment
    }

    public function noShow($id)
    {
        // Logic to mark an appointment as no-show
    }

    public function complete($id)
    {
        // Logic to mark an appointment as completed
    }
}
