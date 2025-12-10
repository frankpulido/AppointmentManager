<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Exceptions\OverlapException;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAppointmentWebRequest;
use App\Models\Practitioner;
use App\Models\AvailableTimeSlot;
use App\Models\AvailableTimeSlotDiagnosis;
use App\Services\IsAlreadyBookedService;
use App\Services\AppointmentCreationService;

/**
 * @OA\Tag(
 *     name="Appointment",
 *     description="Endpoints for Creating and Managing Appointments"
 * )
 */
class AppointmentController extends Controller
{
    public function create(Request $request)
    {
        // Logic to show form for creating a new appointment
    }

    /**
     * @OA\Post(
     *     path="/appointments",
     *     tags={"Appointment"},
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
     *             @OA\Property(property="patient_email", type="string", format="email", example="BxL2B@example.com"),
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
     *                 @OA\Property(property="practitioner_id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-17T19:23:41.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-17T19:23:41.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Esta hora de visita no esta disponible")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="La cita solicitada se superpone con una cita existente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="patient_phone", type="array",
     *                     @OA\Items(type="string", example="El campo patient_phone es obligatorio.")
     *                 ),
     *                 @OA\Property(property="patient_email", type="array",
     *                     @OA\Items(type="string", example="El campo patient_email es obligatorio.")
     *                 ),
     *                 @OA\Property(property="patient_first_name", type="array",
     *                     @OA\Items(type="string", example="El campo patient_first_name es obligatorio.")
     *                 ),
     *                 @OA\Property(property="patient_last_name", type="array",
     *                     @OA\Items(type="string", example="El campo patient_last_name es obligatorio.")
     *                 ),
     *                 @OA\Property(property="kind_of_appointment", type="array",
     *                     @OA\Items(type="string", example="El campo kind_of_appointment es obligatorio.")
     *                 ),
     *                 @OA\Property(property="appointment_date", type="array",
     *                     @OA\Items(type="string", example="El campo appointment_date es obligatorio.")
     *                 ),
     *                 @OA\Property(property="appointment_start_time", type="array",
     *                     @OA\Items(type="string", example="El campo appointment_start_time es obligatorio.")
     *                 ),
     *                 @OA\Property(property="practitioner_id", type="array",
     *                     @OA\Items(type="string", example="El campo practitioner_id es obligatorio.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreAppointmentWebRequest $request)
    {
        $validated = $request->validated();

        $slot = $this->findSlot($validated);
        // Check if the requested slot is available
        if (!$slot) {
            return response()->json(['error' => 'Esta hora de visita no esta disponible'], 400);
        }

        // Add country code (Spain only) to patient phone number
        $validated = $request->validated();
        $validated['patient_phone'] = '+34' . $validated['patient_phone'];
        
        // Check if the patient has already made a booking
        $is_already_booked_service = new IsAlreadyBookedService();
        if ($is_already_booked_service->isAlreadyBooked($validated)) {
            return response()->json(['error' => 'Ya tiene una cita programada con su especialista. Por favor, contáctenos si desea reprogramarla.'], 400);
        }

        // Create the appointment
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
                'patient_phone',
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