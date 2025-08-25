<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        $availableSlots90 = AvailableTimeSlotDiagnosis::all();
        $availableSlots90Filtered = [];

        foreach($availableSlots90 as $slot) {
            $availableSlots90Filtered[$slot->practitioner_id][] = $slot;
        }

        return response()->json([
            'practitioners' => $practitioners,
            'availableSlots90Filtered' =>  $availableSlots90Filtered],
            200
        );
    }

    public function index60()
    {
        // Logic to display available slots for 60-minute appointments
        $practitioners = Practitioner::get()->mapWithKeys(function($p) {
            return [$p->id => $p->first_name . ' ' . $p->last_name];
        })->toArray();

        $availableSlots60 = AvailableTimeSlot::all();
        $availableSlots60Filtered = [];

        foreach($availableSlots60 as $slot) {
            $availableSlots60Filtered[$slot->practitioner_id][] = $slot;
        }

        return response()->json([
            'practitioners' => $practitioners,
            'availableSlots90Filtered' =>  $availableSlots60Filtered],
            200
        );
    }
}
