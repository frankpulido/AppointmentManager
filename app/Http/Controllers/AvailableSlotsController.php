<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AvailableTimeSlotDiagnosis;
use App\Models\AvailableTimeSlot;

class AvailableSlotsController extends Controller
{
    public function index90()
    {
        // Logic to display available slots for 90-minute appointments
        $availableSlots90 = AvailableTimeSlotDiagnosis::all();
        return response()->json($availableSlots90, 200);
    }

    public function index60()
    {
        // Logic to display available slots for 60-minute appointments
        $availableSlots60 = AvailableTimeSlot::all();
        return response()->json($availableSlots60, 200);
    }
}
