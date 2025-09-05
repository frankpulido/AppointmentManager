<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Exceptions\OverlapException;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Practitioner;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentDataAndKindRequest;
use App\Http\Requests\DeleteAppointmentRequest;
use App\Http\Requests\SearchAppointmentByDateTimeRequest;
use App\Http\Requests\SearchAppointmentByPatientNameRequest;
use App\Services\AppointmentCreationService;
use Illuminate\Support\Facades\DB;

class PractitionerAppointmentController extends Controller
{
    public function index()
    {
        // Logic to display appointments
        // Add pagination later if needed
        // Admin can see all appointments, practitioners only their own
        $user = auth('sanctum')->user();

        if ($user->role === 'admin') {
            $practitioners = Practitioner::get()->mapWithKeys(function($p) {
                return [$p->id => $p->first_name . ' ' . $p->last_name];
            })->toArray();

            $appointments = Appointment::query()
                ->orderBy('appointment_date')
                ->orderBy('appointment_start_time')
                ->get()
                ->groupBy('practitioner_id')
                ->toArray();
        } else {
            $practitioners = Practitioner::where('id', $user->practitioner_id)
                ->get()
                ->mapWithKeys(function($p) {
                    return [$p->id => $p->first_name . ' ' . $p->last_name];
                })->toArray();

            $appointments = Appointment::where('practitioner_id', $user->practitioner_id)
                ->orderBy('appointment_date')
                ->orderBy('appointment_start_time')
                ->get()
                ->groupBy('practitioner_id')
                ->toArray();
        }

        return response()->json([
            'practitioners' => $practitioners,
            'appointments' => $appointments],
            200);
    }

    public function create(Request $request)
    {
        // Logic to show form for creating a new appointment
    }

    public function store(StoreAppointmentRequest $request)
    {
        $validated = $request->validated();

        // Use AppointmentCreationService to create new appointment
        // This service also checks for overlap
        try {
            $creationService = new AppointmentCreationService();
            $newAppointment = $creationService->create($validated);
        } catch (OverlapException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(
            [
                'message' => 'La visita ha sido reservada con éxito',
                'appointment' => $newAppointment
            ], 201);
    }

    public function show($id)
    {
        // Logic to display a specific appointment
        $user = auth('sanctum')->user();
        $appointment = Appointment::findOrFail($id);
        
        // Admin can see any appointment, practitioner only their own
        if ($user->role !== 'admin' && $user->practitioner_id !== $appointment->practitioner_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return response()->json($appointment, 200);
    }

    public function edit($id)
    {
        // Logic to show form for editing an existing appointment.
        // Practitioners can only edit patient data, or kind of appointment.
        // Date and time changes must be done via reschedule method.
        // Cancellations must be done via cancel method.
        // No-shows and completion must be done via noShow method.
    }

    public function update(UpdateAppointmentDataAndKindRequest $request, int $appointment_id)
    {
        $validated = $request->validated();
        // Logic to update an existing appointment : change of customer data and/or kind of appointment only
        // Fetch old appointment data and abort if not found
        $appointment = Appointment::find($appointment_id);
        if (!$appointment) {
            return response()->json(['message' => 'La reserva de visita indicada no existe'], 404);
        }

        DB::beginTransaction();
        // Build data for new appointment combining old and new data
        $newData = [
            'practitioner_id'      => $appointment->practitioner_id,
            'appointment_date'     => $appointment->appointment_date->format('Y-m-d'),
            'appointment_start_time' => $appointment->appointment_start_time,
            'appointment_end_time' => null, // We will set it automatically based on kind_of_appointment
            'patient_first_name'   => $validated['patient_first_name'],
            'patient_last_name'    => $validated['patient_last_name'],
            'patient_email'        => $validated['patient_email'] ?? null,
            'patient_phone'        => $validated['patient_phone'],
            'kind_of_appointment'  => $validated['kind_of_appointment'],
            'status'               => $appointment->status,
        ];

        // Since we wrapped this part in transaction it is safe to delete old one first and create new one then
        Appointment::where('id', $appointment->id)->delete();

        try{
            $appointmentCreationService = new AppointmentCreationService();
            $newAppointment = $appointmentCreationService->create($newData);
            DB::commit();
        } catch (OverlapException $e) {
            // If overlap detected, we rollback and return error
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
        
        return response()->json([
            'message' => 'La reserva de visita ha sido actualizada con éxito',
            'appointment' => $newAppointment,
        ], 200);
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