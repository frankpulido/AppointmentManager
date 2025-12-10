<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Practitioner;
use App\Models\AvailableTimeSlot;
use App\Models\AvailableTimeSlotDiagnosis;
use App\Http\Requests\StoreAvailableSlotRequest;
use App\Http\Requests\DeleteAvailableSlotRequest;
use App\Http\Requests\SeedAvailableSlotsRequest;
use App\Services\CheckAppointmentOverlapService;
use App\Services\AvailableTimeSlotSeederService;
use App\Jobs\RegenerateTreatmentSlotsJsonJob;
use App\Jobs\RegenerateDiagnosisSlotsJsonJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * @OA\Tag(
 *     name="PractitionerAvailableSlots",
 *     description="Endpoints for Practitioners to Manage Their Available Time Slots"
 * )
 */
class PractitionerAvailableSlotsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/practitioner/available-slots",
     *     tags={"PractitionerAvailableSlots"},
     *     summary="Get Available Slots for Practitioner",
     *     description="Retrieve available time slots for the authenticated practitioner.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of available slots",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="practitioners",
     *                 type="object",
     *                 description="List of practitioners with their IDs and names"
     *             ),
     *             @OA\Property(
     *                 property="treatment_available_slots",
     *                 type="object",
     *                 description="Available treatment slots grouped by practitioner ID"
     *             ),
     *             @OA\Property(
     *                 property="diagnose_available_slots",
     *                 type="object",
     *                 description="Available diagnosis slots grouped by practitioner ID"
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $user = auth('sanctum')->user();

        if ($user->role === 'admin') {
            
            $practitioners = Practitioner::get()->mapWithKeys(function($p) {
                return [$p->id => $p->first_name . ' ' . $p->last_name];
            })->toArray();
        
            $availableSlotsTreatment = AvailableTimeSlot::query()
                ->orderBy('slot_date')
                ->orderBy('slot_start_time')
                ->get()
                ->groupBy('practitioner_id')
                ->toArray();

            $availableSlotsDiagnosis = AvailableTimeSlotDiagnosis::query()
                ->orderBy('slot_date')
                ->orderBy('slot_start_time')
                ->get()
                ->groupBy('practitioner_id')
                ->toArray();

        } else {

            $practitioners = Practitioner::where('id', $user->practitioner_id)
                ->get()
                ->mapWithKeys(function($p) {
                    return [$p->id => $p->first_name . ' ' . $p->last_name];
                })->toArray();
            
            $availableSlotsTreatment = AvailableTimeSlot::where('practitioner_id', $user->practitioner_id)
                ->orderBy('slot_date')
                ->orderBy('slot_start_time')
                ->get()
                ->groupBy('practitioner_id')
                ->toArray();

            $availableSlotsDiagnosis = AvailableTimeSlotDiagnosis::where('practitioner_id', $user->practitioner_id)
                ->orderBy('slot_date')
                ->orderBy('slot_start_time')
                ->get()
                ->groupBy('practitioner_id')
                ->toArray();
        }

        return response()->json([
            'practitioners' => $practitioners,
            'treatment_available_slots' => $availableSlotsTreatment,
            'diagnose_available_slots' => $availableSlotsDiagnosis
        ], 200); 
    }

    /**
     * @OA\Get(
     *     path="/practitioner/available-slots/treatment",
     *     tags={"PractitionerAvailableSlots"},
     *     summary="Get Available Slots for Treatment (60-minute appointments) for Practitioner",
     *     description="Retrieve available time slots for Treatment appointments for the authenticated practitioner.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of available treatment slots",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="practitioners",
     *                 type="object",
     *                 description="List of practitioners with their IDs and names"
     *             ),
     *             @OA\Property(
     *                 property="treatment_available_slots",
     *                 type="object",
     *                 description="Available treatment slots grouped by practitioner ID"
     *             )
     *         )
     *     )
     * )
     */
    public function indexTreatment()
    {
        $user = auth('sanctum')->user();

        if ($user->role === 'admin') {

            $practitioners = Practitioner::get()->mapWithKeys(function($p) {
                return [$p->id => $p->first_name . ' ' . $p->last_name];
            })->toArray();
        
            $availableSlotsTreatment = AvailableTimeSlot::query()
                ->orderBy('slot_date')
                ->orderBy('slot_start_time')
                ->get()
                ->groupBy('practitioner_id')
                ->toArray();

        } else {

            $practitioners = Practitioner::where('id', $user->practitioner_id)
                ->get()
                ->mapWithKeys(function($p) {
                    return [$p->id => $p->first_name . ' ' . $p->last_name];
                })->toArray();
            
            $availableSlotsTreatment = AvailableTimeSlot::where('practitioner_id', $user->practitioner_id)
                ->orderBy('slot_date')
                ->orderBy('slot_start_time')
                ->get()
                ->groupBy('practitioner_id')
                ->toArray();
        }

        return response()->json([
            'practitioners' => $practitioners,
            'treatment_available_slots' => $availableSlotsTreatment,
        ], 200); 
    }


    /**
     * @OA\Get(
     *     path="/practitioner/available-slots/diagnosis",
     *     tags={"PractitionerAvailableSlots"},
     *     summary="Get Available Slots for Diagnosis (30-minute appointments) for Practitioner",
     *     description="Retrieve available time slots for Diagnosis appointments for the authenticated practitioner.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of available diagnosis slots",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="practitioners",
     *                 type="object",
     *                 description="List of practitioners with their IDs and names"
     *             ),
     *             @OA\Property(
     *                 property="diagnose_available_slots",
     *                 type="object",
     *                 description="Available diagnosis slots grouped by practitioner ID"
     *             )
     *         )
     *     )
     * )
     */
    public function indexDiagnosis()
    {
        $user = auth('sanctum')->user();

        if ($user->role === 'admin') {

            $practitioners = Practitioner::get()->mapWithKeys(function($p) {
                return [$p->id => $p->first_name . ' ' . $p->last_name];
            })->toArray();

            $availableSlotsDiagnosis = AvailableTimeSlotDiagnosis::query()
                ->orderBy('slot_date')
                ->orderBy('slot_start_time')
                ->get()
                ->groupBy('practitioner_id')
                ->toArray();

        } else {

            $practitioners = Practitioner::where('id', $user->practitioner_id)
                ->get()
                ->mapWithKeys(function($p) {
                    return [$p->id => $p->first_name . ' ' . $p->last_name];
                })->toArray();
            
            $availableSlotsDiagnosis = AvailableTimeSlotDiagnosis::where('practitioner_id', $user->practitioner_id)
                ->orderBy('slot_date')
                ->orderBy('slot_start_time')
                ->get()
                ->groupBy('practitioner_id')
                ->toArray();
        }

        return response()->json([
            'practitioners' => $practitioners,
            'diagnose_available_slots' => $availableSlotsDiagnosis
        ], 200); 
    }

    public function create() : void
    {
        // Logic to show form for creating a new available slot
    }

    /**
     * @OA\Post(
     *     path="/practitioner/available-slots",
     *     tags={"PractitionerAvailableSlots"},
     *     summary="Create a new available slot for Practitioner",
     *     description="Endpoint to create a new available time slot for the authenticated practitioner.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"practitioner_id","kind_of_appointment","slot_date","slot_start_time"},
     *             @OA\Property(property="practitioner_id", type="integer", example=1),
     *             @OA\Property(property="kind_of_appointment", type="string", example="diagnose"),
     *             @OA\Property(property="slot_date", type="string", format="date", example="2024-07-15"),
     *             @OA\Property(property="slot_start_time", type="string", format="time", example="10:00:00"),
     *             @OA\Property(property="slot_end_time", type="string", format="time", example="11:30:00", nullable=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Available slot created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="La hora disponible de visita ha sido creada con éxito"),
     *             @OA\Property(property="slot", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="practitioner_id", type="integer", example=1),
     *                 @OA\Property(property="slot_date", type="string", format="date", example="2024-07-15"),
     *                 @OA\Property(property="slot_start_time", type="string", format="time", example="10:00:00"),
     *                 @OA\Property(property="slot_end_time", type="string", format="time", example="11:30:00"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-17T19:23:41.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-17T19:23:41.000000Z"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="La fecha y hora indicadas se solapan con una cita existente")
     *         )
     *     )
     * )
     */
    public function store(StoreAvailableSlotRequest $request) //Request instead of StoreAvailableSlotRequest to debug
    {
        $validated = $request->validated();
        $practitioner_id = $validated['practitioner_id'];
        $practitioner = Practitioner::find($practitioner_id);
        $overlapService = new CheckAppointmentOverlapService();

        // check whether slot_end_time is null and use defaults if so
        if (is_null($validated['slot_end_time'])) {
            if ($validated['kind_of_appointment'] === 'diagnose') {
                $slotDefaultEndTimeDiagnose = $practitioner->calculateEndTime('diagnose', $validated['slot_start_time']);
                $validated['slot_end_time'] = $slotDefaultEndTimeDiagnose;
            } elseif ($validated['kind_of_appointment'] === 'treatment') {
                $slotDefaultEndTimeTreatment = $practitioner->calculateEndTime('treatment', $validated['slot_start_time']);
                $validated['slot_end_time'] = $slotDefaultEndTimeTreatment;
            } else {
                return response()->json(['error' => 'Tipo de cita no válido'], 400);
            }
        }

        // Check for appointment overlap (in case available slot was not removed by AppointmentObserver)
        // This should not happen in normal operation
        if ($overlapService->checkOverlap(
            $validated['slot_date'],
            $validated['slot_start_time'],
            $validated['slot_end_time'],
            $validated['practitioner_id']
        )) {
            return response()->json(['error' => 'La fecha y hora indicadas se solapan con una cita existente'], 400);
        }

        $newSlotData = collect($validated)->except('kind_of_appointment')->toArray();

        // Create new available slot based on kind_of_appointment
        if ($validated['kind_of_appointment'] === 'diagnose') {
            $slot = new AvailableTimeSlotDiagnosis($newSlotData);
        } else {
            $slot = new AvailableTimeSlot($newSlotData);
        }
        $slot->save();

        // Dispatch job to regenerate the appropriate JSON file
        try {
            if ($validated['kind_of_appointment'] === 'treatment') {
                RegenerateTreatmentSlotsJsonJob::dispatch()->onQueue('json-generation');
            } elseif ($validated['kind_of_appointment'] === 'diagnose') {
                RegenerateDiagnosisSlotsJsonJob::dispatch()->onQueue('json-generation');
            }
        } catch (Throwable $e) {
            Log::error('Failed to dispatch slot JSON regeneration jobs: ' . $e->getMessage(), [
                'practitioner_id' => $practitioner_id,
            ]);
        }

        return response()->json([
            'message' => 'La hora disponible de visita ha sido creada con éxito',
            'slot' => $slot
        ], 201);
    }
    
    /**
     * @OA\Delete(
     *     path="/practitioner/available-slots",
     *     tags={"PractitionerAvailableSlots"},
     *     summary="Delete an available slot for Practitioner",
     *     description="Endpoint to delete an available time slot for the authenticated practitioner.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"practitioner_id","kind_of_appointment","slot_id"},
     *             @OA\Property(property="practitioner_id", type="integer", example=1),
     *             @OA\Property(property="kind_of_appointment", type="string", example="diagnose"),
     *             @OA\Property(property="slot_id", type="integer", example=1),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Available slot deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="La hora disponible de visita ha sido eliminada con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Available slot not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="La hora disponible de visita indicada no existe")
     *         )
     *     )
     * )
     */
    public function destroy(DeleteAvailableSlotRequest $request)
    {
        $validated = $request->validated();
        $practitioner_id = $validated['practitioner_id'];
        $slot = null;

        if ($validated['kind_of_appointment'] === 'diagnose') {
            $slot = AvailableTimeSlotDiagnosis::where('id', $validated['slot_id'])
                ->where('practitioner_id', $validated['practitioner_id'])
                ->first();
        }

        if ($validated['kind_of_appointment'] === 'treatment') {
            $slot = AvailableTimeSlot::where('id', $validated['slot_id'])
                ->where('practitioner_id', $validated['practitioner_id'])
                ->first();
        }

        // Dispatch job to regenerate the appropriate JSON file
        if($slot) {
            try {
                if ($validated['kind_of_appointment'] === 'treatment') {
                    RegenerateTreatmentSlotsJsonJob::dispatch()->onQueue('json-generation');
                } elseif ($validated['kind_of_appointment'] === 'diagnose') {
                    RegenerateDiagnosisSlotsJsonJob::dispatch()->onQueue('json-generation');
                }
            } catch (Throwable $e) {
                Log::error('Failed to dispatch slot JSON regeneration jobs: ' . $e->getMessage(), [
                    'practitioner_id' => $practitioner_id,
                ]);
            }
        }
        
        if($slot) {
            $slot->delete();
            return response()->json(['message' => 'La hora disponible de visita ha sido eliminada con éxito'], 200);
        }
        return response()->json(['message' => 'La hora disponible de visita indicada no existe'], 404);
    }

    /**
     * @OA\Post(
     *     path="/practitioner/available-slots/seed",
     *     tags={"PractitionerAvailableSlots"},
     *     summary="Seed Available Slots for Practitioner",
     *     description="Endpoint to seed available time slots for the authenticated practitioner over a specified date range.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"practitioner_id","slots_start_date"},
     *             @OA\Property(property="practitioner_id", type="integer", example=1),
     *             @OA\Property(property="slots_start_date", type="string", format="date", example="2024-07-01"),
     *             @OA\Property(property="slots_end_date", type="string", format="date", example="2024-07-31", nullable=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Available slots seeded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Las horas disponibles de visita han sido generadas con éxito")
     *         )
     *     )
     * )
     */
    public function seed(SeedAvailableSlotsRequest $request)
    {
        $validated = $request->validated();
        $practitioner_id = $validated['practitioner_id'];
        $start_date = $validated['slots_start_date'];
        $end_date = $validated['slots_end_date'] ?? null;

        $seederService = new AvailableTimeSlotSeederService();
        $seederService->seedTreatment($practitioner_id, $start_date, $end_date);
        $seederService->seedDiagnosis($practitioner_id, $start_date, $end_date);

        // Dispatch jobs to regenerate JSON files
        try {
            RegenerateTreatmentSlotsJsonJob::dispatch()->onQueue('json-generation');
            RegenerateDiagnosisSlotsJsonJob::dispatch()->onQueue('json-generation');
        } catch (Throwable $e) {
            Log::error('Failed to dispatch slot JSON regeneration jobs: ' . $e->getMessage(), [
                'practitioner_id' => $practitioner_id,
            ]);
        }

        return response()->json(['message' => 'Las horas disponibles de visita han sido generadas con éxito'], 201);
    }
}