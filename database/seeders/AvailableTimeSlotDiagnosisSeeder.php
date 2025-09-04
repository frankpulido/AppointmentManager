<?php
declare(strict_types=1);
namespace Database\Seeders;

use App\Models\AvailableTimeSlot;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use App\Models\AvailableTimeSlotDiagnosis;
use App\Models\Practitioner;
use App\Services\IsHolidayService;
use App\Services\IsVacationService;

class AvailableTimeSlotDiagnosisSeeder extends Seeder
{
    private array $timeSlots = AvailableTimeSlotDiagnosis::DEFAULT_TIME_SLOTS_DIAGNOSIS;
    /*
    [
        ['08:30:00', '10:00:00'],
        ['10:30:00', '12:00:00'],
        ['12:30:00', '14:00:00'],
        ['15:00:00', '16:30:00'],
        ['17:00:00', '18:30:00'],
        ['19:00:00', '20:30:00'],
    ];
    */
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $practitioners = Practitioner::pluck('id')->toArray();
        $holidayService = new IsHolidayService();
        $vacationService = new IsVacationService();

        $start = Carbon::today();
        $end = Carbon::create($start->year + 1, 12, 31);

        for ($date = $start; $date->lte($end); $date->addDay()) {
            if ($date->isWeekend()) {
                continue; // skip Saturday and Sunday
            }

            if ($holidayService->isDateHoliday($date)) {
                continue; // skip holidays
            }

            foreach ($practitioners as $practitioner_id) {
                if ($vacationService->isDateInVacation($practitioner_id, $date)) {
                    continue; // skip vacation days
                }

                foreach ($this->timeSlots as [$startTime, $endTime]) {
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
        }
    }
}