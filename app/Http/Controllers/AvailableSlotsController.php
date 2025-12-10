<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\AvailableTimeSlotDiagnosis;
use App\Models\AvailableTimeSlot;
use App\Models\Practitioner;
//use App\Models\Appointment;

/**
 * @OA\Tag(
 *     name="AvailableSlots",
 *     description="Endpoints for Retrieving Available Time Slots for Diagnosis and Treatment"
 * )
 */

class AvailableSlotsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/available-slots/diagnosis",
     *     tags={"AvailableSlots"},
     *     summary="Get Available Slots for Diagnosis (90-minute appointments)",
     *     description="Retrieve available time slots for Diagnosis appointments grouped by practitioner.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of available diagnosis slots",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="practitioners",
     *                 type="object",
     *                 description="List of practitioners with their IDs, names, and diagnosis prices"
     *             ),
     *             @OA\Property(
     *                 property="available_slots_diagnose",
     *                 type="object",
     *                 description="Available diagnosis slots grouped by practitioner ID"
     *             )
     *         )
     *     )
     * )
     */
    public function indexDiagnosis()
    {
        // Logic to display available slots for Diagnosis, 90-minute appointments
        $practitioners = Practitioner::get()->mapWithKeys(function($p) {
            return [$p->id => [$p->first_name . ' ' . $p->last_name, $p->custom_settings['price_diagnosis']]];
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

        return response()->json([
            'practitioners' => $practitioners,
            'available_slots_diagnose' =>  $available_diagnosis_slots_by_practitioner->toArray()],
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/available-slots/treatment",
     *     tags={"AvailableSlots"},
     *     summary="Get Available Slots for Treatment (60-minute appointments)",
     *     description="Retrieve available time slots for Treatment appointments grouped by practitioner.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of available treatment slots",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="practitioners",
     *                 type="object",
     *                 description="List of practitioners with their IDs, names, and treatment prices"
     *             ),
     *             @OA\Property(
     *                 property="available_slots_treatment",
     *                 type="object",
     *                 description="Available treatment slots grouped by practitioner ID"
     *             )
     *         )
     *     )
     * )
     */
    public function indexTreatment()
    {
        // Logic to display available slots for Treatment, 60-minute appointments
        $practitioners = Practitioner::get()->mapWithKeys(function($p) {
            return [$p->id => [$p->first_name . ' ' . $p->last_name, $p->custom_settings['price_treatment']]];
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

        return response()->json([
            'practitioners' => $practitioners,
            'available_slots_treatment' =>  $available_treatment_slots_by_practitioner->toArray()],
            200
        );
    }
}