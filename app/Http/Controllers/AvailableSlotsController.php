<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\AvailableTimeSlotDiagnosis;
use App\Models\AvailableTimeSlot;
use App\Models\Practitioner;

class AvailableSlotsController extends Controller
{
    public function index90()
    {
        // Logic to display available slots for 90-minute appointments
        $practitioners = Practitioner::get()->mapWithKeys(function($p) {
            return [$p->id => $p->first_name . ' ' . $p->last_name];
        })->toArray();
        
        $availableSlots90 = AvailableTimeSlotDiagnosis::query()
            ->orderBy('slot_date')
            ->orderBy('slot_start_time')
            ->get();
        $availableSlots90Filtered = $availableSlots90
            ->groupBy('practitioner_id')
            ->toArray();

        return response()->json([
            'practitioners' => $practitioners,
            'available_slots_diagnose' =>  $availableSlots90Filtered],
            200
        );
    }

    public function index60()
    {
        // Logic to display available slots for 60-minute appointments
        $practitioners = Practitioner::get()->mapWithKeys(function($p) {
            return [$p->id => $p->first_name . ' ' . $p->last_name];
        })->toArray();

        $availableSlots60 = AvailableTimeSlot::query()
            ->orderBy('slot_date')
            ->orderBy('slot_start_time')
            ->get();
        $availableSlots60Filtered = $availableSlots60
            ->groupBy('practitioner_id')
            ->toArray();

        return response()->json([
            'practitioners' => $practitioners,
            'available_slots_treatment' =>  $availableSlots60Filtered],
            200
        );
    }
}
