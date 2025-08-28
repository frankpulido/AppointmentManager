<?php
declare(strict_types=1);
namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use App\Models\AvailableTimeSlot;
use App\Models\Practitioner;
use App\Services\IsHolidayService;
use App\Services\IsVacationService;

class AvailableTimeSlotSeeder extends Seeder
{
    private array $timeSlots = [
        ['08:30:00', '09:30:00'],
        ['10:00:00', '11:00:00'],
        ['11:15:00', '12:15:00'],
        ['12:30:00', '13:30:00'],
        ['15:00:00', '16:00:00'],
        ['16:15:00', '17:15:00'],
        ['17:30:00', '18:30:00'],
        ['18:45:00', '19:45:00'],
    ];
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
        }
    }
}