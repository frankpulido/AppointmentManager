<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\AvailableTimeSlotDiagnosis;
use App\Models\AvailableTimeSlot;
use App\Models\Practitioner;
//use App\Models\Appointment;

class AvailableSlotsController extends Controller
{
    public function indexDiagnosis()
    {
        // Logic to display available slots for Diagnosis, 90-minute appointments
        $practitioners = Practitioner::get()->mapWithKeys(function($p) {
            return [$p->id => $p->first_name . ' ' . $p->last_name];
        })->toArray();
        
        $all_diagnosis_slots = AvailableTimeSlotDiagnosis::with('practitioner')->get();

        $available_diagnosis_slots_by_practitioner = $all_diagnosis_slots->filter(function ($slot) {
            $max_days_ahead = $slot->practitioner->getPractitionerSetting('max_days_ahead');
            return $slot->slot_date <= now()->addDays($max_days_ahead);
        })
        ->each(function ($slot) {
            $slot->makeHidden('practitioner');
        })
        ->groupBy('practitioner_id');

        /*
        $practitioners_availability = Practitioner::all();
        $available_diagnosis_slots_by_practitioner = collect();
        foreach($practitioners_availability as $practitioner) {
            $max_days_ahead = $practitioner->getPractitionerSetting('max_days_ahead');
            $slots = AvailableTimeSlotDiagnosis::query()
                ->where('practitioner_id', $practitioner->id)
                ->where('slot_date', '<=', now()->addDays($max_days_ahead)->toDateString())
                ->get();
            $available_diagnosis_slots_by_practitioner->put($practitioner->id, $slots);
        }

        $available_diagnosis_slots_by_practitioner_array = $available_diagnosis_slots_by_practitioner->toArray();
        */

        return response()->json([
            'practitioners' => $practitioners,
            /*
            'available_slots_diagnose' =>  $available_diagnosis_slots_by_practitioner->only([
                'id',
                'practitioner_id',
                'slot_date',
                'slot_start_time',
                'slot_end_time',
            ])],
            */
            'available_slots_diagnose' =>  $available_diagnosis_slots_by_practitioner->toArray()],
            200
        );
    }

    public function indexTreatment()
    {
        // Logic to display available slots for Treatment, 60-minute appointments
        $practitioners = Practitioner::get()->mapWithKeys(function($p) {
            return [$p->id => $p->first_name . ' ' . $p->last_name];
        })->toArray();

        $all_treatment_slots = AvailableTimeSlot::with('practitioner')->get();
 
        $available_treatment_slots_by_practitioner = $all_treatment_slots->filter(function ($slot) {
            $max_days_ahead = $slot->practitioner->getPractitionerSetting('max_days_ahead');
            return $slot->slot_date <= now()->addDays($max_days_ahead);
        })
        ->each(function ($slot) {
            $slot->makeHidden('practitioner');
        })
        ->groupBy('practitioner_id');

        /*
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
        */

        return response()->json([
            'practitioners' => $practitioners,
            'available_slots_treatment' =>  $available_treatment_slots_by_practitioner->toArray()],
            200
        );
    }
}