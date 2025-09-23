<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\AvailableTimeSlotDiagnosis;
use App\Models\AvailableTimeSlot;
use App\Models\Practitioner;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;

class AvailableSlotsController extends Controller
{
    public function index90()
    {
        // Logic to display available slots for 90-minute appointments
        $practitioners = Practitioner::get()->mapWithKeys(function($p) {
            return [$p->id => $p->first_name . ' ' . $p->last_name];
        })->toArray();

        /*
        $max_days_ahead = Appointment::DEFAULT_MAX_ONLINE_APPOINTMENTS_DAYS_AHEAD; // 91 days ahead from today
        
        $availableSlots90 = AvailableTimeSlotDiagnosis::query()
            ->where('slot_date', '<=', now()->addDays($max_days_ahead)->toDateString())
            ->orderBy('slot_date')
            ->orderBy('slot_start_time')
            ->get();
        $availableSlots90Filtered = $availableSlots90
            ->groupBy('practitioner_id')
            ->toArray();
        */

        $practitioners_availability = Practitioner::all();
        $availableSlots90 = collect();
        foreach($practitioners_availability as $practitioner) {
            $max_days_ahead = $practitioner->getPractitionerSetting('max_days_ahead');
            $slots = AvailableTimeSlotDiagnosis::query()
                ->where('practitioner_id', $practitioner->id)
                ->where('slot_date', '<=', now()->addDays($max_days_ahead)->toDateString())
                ->get();
                //->groupBy('slot_date');
            $availableSlots90->put($practitioner->id, $slots);
        }

        $availableSlots90Filtered = $availableSlots90->toArray();

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

        /*
        $max_days_ahead = Appointment::DEFAULT_MAX_ONLINE_APPOINTMENTS_DAYS_AHEAD; // 91 days ahead from today

        $availableSlots60 = AvailableTimeSlot::query()
            ->where('slot_date', '<=', now()->addDays($max_days_ahead)->toDateString())
            ->orderBy('slot_date')
            ->orderBy('slot_start_time')
            ->get();
        $availableSlots60Filtered = $availableSlots60
            ->groupBy('practitioner_id')
            ->toArray();
        */

        $practitioners_availability = Practitioner::all();
        $availableSlots60 = collect();
        foreach($practitioners_availability as $practitioner) {
            $max_days_ahead = $practitioner->getPractitionerSetting('max_days_ahead');
            $slots = AvailableTimeSlot::query()
                ->where('practitioner_id', $practitioner->id)
                ->where('slot_date', '<=', now()->addDays($max_days_ahead)->toDateString())
                ->get();
                //->groupBy('slot_date');
            $availableSlots60->put($practitioner->id, $slots);
        }

        $availableSlots60Filtered = $availableSlots60->toArray();

        return response()->json([
            'practitioners' => $practitioners,
            'available_slots_treatment' =>  $availableSlots60Filtered],
            200
        );
    }
}
