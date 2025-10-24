<?php
declare(strict_types=1);
namespace App\Services;
// This Service creates new Appointments
use Illuminate\Support\Carbon;
use App\Models\AvailableTimeSlot;
use App\Models\AvailableTimeSlotDiagnosis;
use App\Models\Practitioner;
use App\Services\IsHolidayService;
use App\Services\IsVacationService;

class AvailableTimeSlotSeederService
{
    /**
     * Seeds available time slots for Treatment appointments for the given practitioner.
     *
     * @param int $practitioner_id  // Practitioner ID
     * @param string $start_date    // Date in 'Y-m-d' format from request
     * @param string $end_date      // Date in 'Y-m-d' format from request
     * @return void
     */
    public function seedTreatment(int $practitioner_id, string $start_date, ?string $end_date = null)
    {
        $timeSlotsTreatment = Practitioner::find($practitioner_id)->getPractitionerSetting('treatment_slots');
        $this->seedSlots($practitioner_id, $start_date, $end_date, $timeSlotsTreatment, AvailableTimeSlot::class);
    }

    /**
     * Seeds available time slots for Diagnosis appointments for the given practitioner.
     *
     * @param int $practitioner_id  // Practitioner ID
     * @param string $start_date    // Date in 'Y-m-d' format from request
     * @param string $end_date      // Date in 'Y-m-d' format from request
     * @return void
     */
    public function seedDiagnosis(int $practitioner_id, string $start_date, ?string $end_date = null)
    {
        $timeSlotsDiagnosis = Practitioner::find($practitioner_id)->getPractitionerSetting('diagnosis_slots');
        $this->seedSlots($practitioner_id, $start_date, $end_date, $timeSlotsDiagnosis, AvailableTimeSlotDiagnosis::class);
    }

    private function seedSlots(
        int $practitioner_id, 
        string $start_date, 
        ?string $end_date, 
        array $timeSlots, 
        string $modelClass
    ): void
    {
        $holidayService = new IsHolidayService();
        $vacationService = new IsVacationService();
        $ppointmentOverlapService = new CheckAppointmentOverlapService();
        $start = Carbon::parse($start_date);
        $end = $end_date ? Carbon::parse($end_date) : $start;

        for ($date = $start; $date->lte($end); $date->addDay()) {
            if ($date->isWeekend() || 
                $holidayService->isDateHoliday($date) || 
                $vacationService->isDateInVacation($practitioner_id, $date)) {
                continue;
            }

            foreach ($timeSlots as [$startTime, $endTime]) {
                // Check for overlapping appointments before creating the slot
                if ($ppointmentOverlapService->checkOverlap($date->toDateString(), $startTime, $endTime, $practitioner_id)) {
                    continue; // Skip this slot
                }
                $modelClass::updateOrCreate([
                    'practitioner_id' => $practitioner_id,
                    'slot_date' => $date,
                    'slot_start_time' => $startTime,
                    'slot_end_time' => $endTime,
                ]);
            }
        }
    }
}