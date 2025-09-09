<?php
declare(strict_types=1);
namespace App\Services;
// This Service creates new Appointments
use Illuminate\Support\Carbon;
use App\Models\AvailableTimeSlot;
use App\Models\AvailableTimeSlotDiagnosis;
use App\Services\IsHolidayService;
use App\Services\IsVacationService;

class AvailableTimeSlotSeederService
{
    private array $timeSlotsTreatment = AvailableTimeSlot::DEFAULT_TIME_SLOTS_TREATMENT;
    private array $timeSlotsDiagnosis = AvailableTimeSlotDiagnosis::DEFAULT_TIME_SLOTS_DIAGNOSIS;

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
        $this->seedSlots($practitioner_id, $start_date, $end_date, $this->timeSlotsTreatment, AvailableTimeSlot::class);
        /*
        $holidayService = new IsHolidayService();
        $vacationService = new IsVacationService();
        $start = Carbon::parse($start_date);
        if ($end_date === null) {
            $end_date = $start_date;
        }
        $end = Carbon::parse($end_date);

        for ($date = $start; $date->lte($end); $date->addDay()) {
            if ($date->isWeekend()) {
                continue; // skip Saturday and Sunday
            }

            if ($holidayService->isDateHoliday($date)) {
                continue; // skip holidays
            }

            if ($vacationService->isDateInVacation($practitioner_id, $date)) {
                continue; // skip vacation days
            }

            foreach ($this->timeSlotsTreatment as [$startTime, $endTime]) {
                AvailableTimeSlot::updateOrCreate(
                    [
                        'practitioner_id' => $practitioner_id,
                        'slot_date' => $date,
                        'slot_start_time' => $startTime,
                        'slot_end_time' => $endTime,
                    ]
                );
            }
        }
        */
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
        $this->seedSlots($practitioner_id, $start_date, $end_date, $this->timeSlotsDiagnosis, AvailableTimeSlotDiagnosis::class);
        /*
        $holidayService = new IsHolidayService();
        $vacationService = new IsVacationService();
        $start = Carbon::parse($start_date);
        if ($end_date === null) {
            $end_date = $start_date;
        }
        $end = Carbon::parse($end_date);

        for ($date = $start; $date->lte($end); $date->addDay()) {
            if ($date->isWeekend()) {
                continue; // skip Saturday and Sunday
            }

            if ($holidayService->isDateHoliday($date)) {
                continue; // skip holidays
            }

            if ($vacationService->isDateInVacation($practitioner_id, $date)) {
                continue; // skip vacation days
            }

            foreach ($this->timeSlotsDiagnosis as [$startTime, $endTime]) {
                AvailableTimeSlotDiagnosis::updateOrCreate(
                    [
                        'practitioner_id' => $practitioner_id,
                        'slot_date' => $date,
                        'slot_start_time' => $startTime,
                        'slot_end_time' => $endTime,
                    ]
                );
            }
        }
        */
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
        $start = Carbon::parse($start_date);
        $end = $end_date ? Carbon::parse($end_date) : $start;

        for ($date = $start; $date->lte($end); $date->addDay()) {
            if ($date->isWeekend() || 
                $holidayService->isDateHoliday($date) || 
                $vacationService->isDateInVacation($practitioner_id, $date)) {
                continue;
            }

            foreach ($timeSlots as [$startTime, $endTime]) {
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