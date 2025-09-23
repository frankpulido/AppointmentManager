<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\AvailableTimeSlotDiagnosis;
use App\Models\AvailableTimeSlot;
use App\Models\Practitioner;
use App\Models\Appointment;

class AvailableSlotsController extends Controller
{
    public function indexDiagnosis()
    {
        // Logic to display available slots for Diagnosis, 90-minute appointments
        $practitioners = Practitioner::get()->mapWithKeys(function($p) {
            return [$p->id => $p->first_name . ' ' . $p->last_name];
        })->toArray();

        $practitioners_availability = Practitioner::all();
        $availableSlots90 = collect();
        foreach($practitioners_availability as $practitioner) {
            $max_days_ahead = $practitioner->getPractitionerSetting('max_days_ahead');
            $slots = AvailableTimeSlotDiagnosis::query()
                ->where('practitioner_id', $practitioner->id)
                ->where('slot_date', '<=', now()->addDays($max_days_ahead)->toDateString())
                ->get();
            $availableSlots90->put($practitioner->id, $slots);
        }

        $availableSlots90Filtered = $availableSlots90->toArray();

        return response()->json([
            'practitioners' => $practitioners,
            'available_slots_diagnose' =>  $availableSlots90Filtered],
            200
        );
    }

    public function indexTreatment()
    {
        // Logic to display available slots for Treatment, 60-minute appointments
        $practitioners = Practitioner::get()->mapWithKeys(function($p) {
            return [$p->id => $p->first_name . ' ' . $p->last_name];
        })->toArray();

        $practitioners_availability = Practitioner::all();
        $availableSlotsTreatment = collect();
        foreach($practitioners_availability as $practitioner) {
            $max_days_ahead = $practitioner->getPractitionerSetting('max_days_ahead');
            $slots = AvailableTimeSlot::query()
                ->where('practitioner_id', $practitioner->id)
                ->where('slot_date', '<=', now()->addDays($max_days_ahead)->toDateString())
                ->get();
            $availableSlotsTreatment->put($practitioner->id, $slots);
        }

        $availableSlotsTreatmentFiltered = $availableSlotsTreatment->toArray();

        return response()->json([
            'practitioners' => $practitioners,
            'available_slots_treatment' =>  $availableSlotsTreatmentFiltered],
            200
        );
    }
}