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

/**
 * @OA\Tag(
 *     name="PractitionerAppointment",
 *     description="Endpoints for Practitioners to Manage Their Appointments"
 * )
 */
class PractitionerAppointmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/practitioner/appointments",
     *     tags={"PractitionerAppointment"},
     *     summary="Get all appointments for the authenticated practitioner",
     *     description="Retrieve a list of all appointments for the logged-in practitioner. Admins can see all appointments.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of appointments",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="practitioners",
     *                 type="object",
     *                 example={"1": "John Doe", "2": "Jane Smith"}
     *             ),
     *             @OA\Property(
     *                 property="appointments",
     *                 type="object",
     *                 example={
     *                     "1": {
     *                         {
     *                             "id": 1,
     *                             "practitioner_id": 1,
     *                             "kind_of_appointment": "diagnose",
     *                             "appointment_date": "2024-07-15",
     *                             "appointment_start_time": "10:00:00",
     *                             "appointment_end_time": "10:30:00",
     *                             "patient_first_name": "John",
     *                             "patient_last_name": "Doe",
     *                             "patient_email": "BxL2B@example.com",
     *                             "patient_phone": "123456789",
     *                             "status": "pending"
     *                         },
     *                         {
     *                             "id": 2,
     *                             "practitioner_id": 1,
     *                             "kind_of_appointment": "diagnose",
     *                             "appointment_date": "2024-07-15",
     *                             "appointment_start_time": "11:00:00",
     *                             "appointment_end_time": "11:30:00",
     *                             "patient_first_name": "Jane",
     *                             "patient_last_name": "Smith",
     *                             "patient_email": "Ktq9t@example.com",
     *                             "patient_phone": "987654321",
     *                             "status": "pending"
     *                         }
     *                     }
     *                 }
     *             )
     *         )
     *     )
     * )
     */
     
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

    /**
     * @OA\Post(
     *     path="/practitioner/appointments",
     *     tags={"PractitionerAppointment"},
     *     summary="Create a new appointment",
     *     description="Endpoint to create a new appointment for diagnosis or treatment.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"practitioner_id","kind_of_appointment","appointment_date","appointment_start_time","patient_first_name","patient_last_name","patient_email","patient_phone"},
     *             @OA\Property(property="practitioner_id", type="integer", example=1),
     *             @OA\Property(property="kind_of_appointment", type="string", example="diagnose"),
     *             @OA\Property(property="appointment_date", type="string", format="date", example="2024-07-15"),
     *             @OA\Property(property="appointment_start_time", type="string", format="time", example="10:00:00"),
     *             @OA\Property(property="patient_first_name", type="string", example="John"),
     *             @OA\Property(property="patient_last_name", type="string", example="Doe"),
     *             @OA\Property(property="patient_email", type="string", example="BxL2B@example.com"),
     *             @OA\Property(property="patient_phone", type="string", example="123456789"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Appointment created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Su cita ha sido reservada con éxito"),
     *             @OA\Property(property="appointment", type="object",
     *                 @OA\Property(property="kind_of_appointment", type="string", example="diagnose"),
     *                 @OA\Property(property="appointment_date", type="string", format="date", example="2024-07-15"),
     *                 @OA\Property(property="appointment_start_time", type="string", format="time", example="10:00:00"),
     *                 @OA\Property(property="patient_first_name", type="string", example="John"),
     *                 @OA\Property(property="patient_last_name", type="string", example="Doe"),
     *                 @OA\Property(property="patient_email", type="string", format="email", example="BxL2B@example.com"),
     *                 @OA\Property(property="patient_phone", type="string", example="123456789"),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Overlap detected for the requested appointment time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Esta hora de visita no esta disponible")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     */
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
                'appointment' => $newAppointment->only([
                'id',
                'practitioner_id',
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

    /**
     * @OA\Get(
     *     path="/practitioner/appointments/{id}",
     *     tags={"PractitionerAppointments"},
     *     summary="Get a specific appointment",
     *     description="Retrieve details of a specific appointment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the appointment to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of appointment",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="practitioner_id", type="integer", example=1),
     *             @OA\Property(property="kind_of_appointment", type="string", example="diagnose"),
     *             @OA\Property(property="appointment_date", type="string", format="date", example="2024-07-15"),
     *             @OA\Property(property="appointment_start_time", type="string", format="time", example="10:00:00"),
     *             @OA\Property(property="appointment_end_time", type="string", format="time", example="10:30:00"),
     *             @OA\Property(property="patient_first_name", type="string", example="John"),
     *             @OA\Property(property="patient_last_name", type="string", example="Doe"),
     *             @OA\Property(property="patient_email", type="string", example="BxL2B@example.com"),
     *             @OA\Property(property="status", type="string", example="pending"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="La reserva de visita indicada no existe")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/practitioner/appointments/{id}",
     *     tags={"PractitionerAppointment"},
     *     summary="Update an existing appointment",
     *     description="Endpoint to update patient data and/or kind of appointment for an existing appointment.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the appointment to update",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"patient_first_name","patient_last_name","patient_phone","kind_of_appointment"},
     *             @OA\Property(property="patient_first_name", type="string", example="John"),
     *             @OA\Property(property="patient_last_name", type="string", example="Doe"),
     *             @OA\Property(property="patient_email", type="string", example="BxL2B@example.com"),
     *             @OA\Property(property="patient_phone", type="string", example="123456789"),
     *             @OA\Property(property="kind_of_appointment", type="string", example="diagnose"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="La reserva de visita ha sido actualizada con éxito"),
     *             @OA\Property(property="appointment", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="practitioner_id", type="integer", example=1),
     *                 @OA\Property(property="kind_of_appointment", type="string", example="diagnose"),
     *                 @OA\Property(property="appointment_date", type="string", format="date", example="2024-07-15"),
     *                 @OA\Property(property="appointment_start_time", type="string", format="time", example="10:00:00"),
     *                 @OA\Property(property="appointment_end_time", type="string", format="time", example="10:30:00"),
     *                 @OA\Property(property="patient_first_name", type="string", example="John"),
     *                 @OA\Property(property="patient_last_name", type="string", example="Doe"),
     *                 @OA\Property(property="patient_email", type="string", example="BxL2B@example.com"),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="La reserva de visita indicada no existe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Overlap detected for the requested appointment time")
     *         )
     *     )
     * )
     */
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
            'appointment' => $newAppointment->only([
                'id',
                'practitioner_id',
                'kind_of_appointment',
                'appointment_date',
                'appointment_start_time',
                'appointment_end_time',
                'patient_first_name',
                'patient_last_name',
                'patient_email',
                'patient_phone',
                'status',
            ])],
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/practitioner/appointments",
     *     tags={"PractitionerAppointment"},
     *     summary="Delete an appointment",
     *     description="Endpoint to delete an existing appointment.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"practitioner_id","appointment_id"},
     *             @OA\Property(property="practitioner_id", type="integer", example=1),
     *             @OA\Property(property="appointment_id", type="integer", example=1),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="La reserva de visita ha sido eliminada con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="La reserva de visita indicada no existe")
     *         )
     *     )
     * )
     */
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

    
    /**
     * @OA\Post(
     *     path="/practitioner/appointments/search-by-datetime",
     *     tags={"PractitionerAppointment"},
     *     summary="Search appointments by date, time, and kind",
     *     description="Endpoint to search for appointments based on date, time, and kind of appointment for a specific practitioner.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"practitioner_id","appointment_date","appointment_start_time","appointment_end_time","kind_of_appointment"},
     *             @OA\Property(property="practitioner_id", type="integer", example=1),
     *             @OA\Property(property="appointment_date", type="string", format="date", example="2024-07-15"),
     *             @OA\Property(property="appointment_start_time", type="string", format="time", example="10:00:00"),
     *             @OA\Property(property="appointment_end_time", type="string", format="time", example="10:30:00"),
     *             @OA\Property(property="kind_of_appointment", type="string", example="diagnose"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of appointments",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="appointments",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="practitioner_id", type="integer", example=1),
     *                     @OA\Property(property="kind_of_appointment", type="string", example="diagnose"),
     *                     @OA\Property(property="appointment_date", type="string", format="date", example="2024-07-15"),
     *                     @OA\Property(property="appointment_start_time", type="string", format="time", example="10:00:00"),
     *                     @OA\Property(property="appointment_end_time", type="string", format="time", example="10:30:00"),
     *                     @OA\Property(property="patient_first_name", type="string", example="John"),
     *                     @OA\Property(property="patient_last_name", type="string", example="Doe"),
     *                     @OA\Property(property="patient_email", type="string", example="BxL2B@example.com"),
     *                     @OA\Property(property="patient_phone", type="string", example="123456789"),
     *                     @OA\Property(property="status", type="string", example="pending"),
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/practitioner/appointments/search-by-patient-name",
     *     tags={"PractitionerAppointment"},
     *     summary="Search appointments by patient name",
     *     description="Endpoint to search for appointments based on patient first and last name for a specific practitioner.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"practitioner_id","patient_first_name","patient_last_name"},
     *             @OA\Property(property="practitioner_id", type="integer", example=1),
     *             @OA\Property(property="patient_first_name", type="string", example="John"),
     *             @OA\Property(property="patient_last_name", type="string", example="Doe"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of appointments",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="appointments",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="practitioner_id", type="integer", example=1),
     *                     @OA\Property(property="kind_of_appointment", type="string", example="diagnose"),
     *                     @OA\Property(property="appointment_date", type="string", format="date", example="2024-07-15"),
     *                     @OA\Property(property="appointment_start_time", type="string", format="time", example="10:00:00"),
     *                     @OA\Property(property="appointment_end_time", type="string", format="time", example="10:30:00"),
     *                     @OA\Property(property="patient_first_name", type="string", example="John"),
     *                     @OA\Property(property="patient_last_name", type="string", example="Doe"),
     *                     @OA\Property(property="patient_email", type="string", example="BxL2B@example.com"),
     *                     @OA\Property(property="patient_phone", type="string", example="123456789"),
     *                     @OA\Property(property="status", type="string", example="pending"),
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function searchAppointmentByPatientName(SearchAppointmentByPatientNameRequest $request)
    {
        // Logic to search for appointments based on PATIENT NAME
        $validated = $request->validated;
        $appointments = Appointment::where('practitioner_id', $validated['practitioner_id'])
            ->where('patient_first_name', 'LIKE', $validated['patient_first_name'])
            ->where('patient_last_name', 'LIKE', $validated['patient_last_name'])
            ->get();
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