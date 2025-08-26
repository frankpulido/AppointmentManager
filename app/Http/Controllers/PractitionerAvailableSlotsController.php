<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AvailableTimeSlot;
use App\Models\AvailableTimeSlotDiagnosis;
use App\Http\Requests\StoreAvailableSlotRequest;
use App\Http\Requests\DeleteAvailableSlotRequest;
use App\Services\CheckAppointmentOverlapService;

class PractitionerAvailableSlotsController extends Controller
{
    public function index()
    {
        // Add sanctum and token, authorized id in route
        $availableSlots60 = AvailableTimeSlot::query()
            ->orderBy('slot_date')
            ->orderBy('slot_start_time')
            ->get(); 
        $availableSlots90 = AvailableTimeSlotDiagnosis::query()
            ->orderBy('slot_date')
            ->orderBy('slot_start_time')
            ->get();
        return response()->json([
            'treatment_available_slots' => $availableSlots60,
            'diagnose_available_slots' => $availableSlots90
        ], 200); 
    }

    public function index60()
    {
        // Add sanctum and token, authorized id in route
        $availableSlots60 = AvailableTimeSlot::query()
            ->orderBy('slot_date')
            ->orderBy('slot_start_time')
            ->get(); 
        return response()->json(['treatment_available_slots' => $availableSlots60], 200); 
    }

    public function index90()
    {
        // Add sanctum and token, authorized id in route
        $availableSlots90 = AvailableTimeSlotDiagnosis::query()
            ->orderBy('slot_date')
            ->orderBy('slot_start_time')
            ->get();
        return response()->json(['diagnose_available_slots' => $availableSlots90], 200);
    }

    public function create() : void
    {
        // Logic to show form for creating a new available slot
    }

    public function store(StoreAvailableSlotRequest $request) //Request instead of StoreAvailableSlotRequest to debug
    {
        $validated = $request->validated();
        $overlapService = new CheckAppointmentOverlapService();

        // Check for appointment overlap
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

        return response()->json([
            'message' => 'La hora disponible de visita ha sido creada con éxito',
            'slot' => $slot
        ], 201);
    }
    
    public function destroy(DeleteAvailableSlotRequest $request)
    {
        $validated = $request->validated();
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
        
        if($slot) {
            $slot->delete();
            return response()->json(['message' => 'La hora disponible de visita ha sido eliminada con éxito'], 200);
        }
        return response()->json(['message' => 'La hora disponible de visita indicada no existe'], 404);
    }
}