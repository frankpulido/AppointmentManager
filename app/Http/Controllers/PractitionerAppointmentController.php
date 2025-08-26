<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\SearchAppointmentByDateTimeRequest;
use App\Http\Requests\SearchAppointmentByPatientNameRequest;
use App\Http\Requests\DeleteAppointmentRequest;
use App\Services\CheckAppointmentOverlapService;

class PractitionerAppointmentController extends Controller
{
    public function index()
    {
        // Logic to display appointments
        // Add sanctum and token, authorized id in route
        // Add pagination
        // changed query() to where('practitioner_id', $user_id)
        $appointments = Appointment::query()
            ->orderBy('appointment_date')
            ->orderBy('appointment_start_time')
            ->get();
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

    public function destroy(DeleteAppointmentRequest $request)
    {
        $validated = $request->validated();
        $appointment = Appointment::where('practitioner_id', $validated['practitioner_id'])
            ->where('id', $validated['appointment_id'])
            ->first();
        if(!$appointment) {
            return response()->json(['message' => 'La reserva de visita indicada no existe'], 404);
        }
        $appointment->delete();
        return response()->json(['message' => 'La reserva de visita ha sido eliminada con éxito'], 200);
    }

    public function searchAppointmentByDateTime(SearchAppointmentByDateTimeRequest $request)
    {
        // Logic to search for appointments based on PRACTITIONER and date/time/kind
        $validated = $request->validated();
        $appointment = Appointment::where('practitioner_id', $validated['practitioner_id'])
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->where('appointment_start_time', $validated['appointment_start_time'])
            ->where('appointment_end_time', $validated['appointment_end_time'])
            ->where('kind_of_appointment', $validated['kind_of_appointment'])
            ->get();
        return response()->json($appointment, 200);
    }

    public function searchAppointmentByPatientName(SearchAppointmentByPatientNameRequest $request)
    {
        // Logic to search for appointments based on PATIENT NAME
        $validated = $request->validated;
        $appointments = Appointment::where('practitioner_id', $validated['practitioner_id'])
            ->where('patient_first_name', 'LIKE', $validated['patient_first_name'])
            ->where('patient_last_name', 'LIKE', $validated['patient_last_name'])
            ->get;
        return response()->json($appointments, 200);
    }

    public function searchAppointmentByPatientEmail(Request $request)
    {
        // Logic to search for appointments based on PATIENT EMAIL
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
