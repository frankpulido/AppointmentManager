<?php
declare(strict_types=1);
namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Carbon;
//use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
//use App\Models\Holiday;
//use App\Models\Vacation;
use App\Models\AvailableTimeSlotDiagnosis;
use App\Models\Practitioner;
use App\Services\IsHolidayService;
use App\Services\IsVacationService;

class AvailableTimeSlotDiagnosisSeeder extends Seeder
{
    private array $timeSlots = [
        ['08:30:00', '10:00:00'],
        ['10:30:00', '12:00:00'],
        ['12:30:00', '14:00:00'],
        ['15:00:00', '16:30:00'],
        ['17:00:00', '18:30:00'],
        ['19:00:00', '20:30:00'],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //$holidays = Holiday::pluck('date')->toArray();
        $practitioners = Practitioner::pluck('id')->toArray();
        $holidayService = new IsHolidayService();
        $vacationService = new IsVacationService();

        $start = Carbon::today();
        $end = Carbon::create($start->year + 1, 12, 31);

        for ($date = $start; $date->lte($end); $date->addDay()) {
            if ($date->isWeekend()) {
                continue; // skip Saturday and Sunday
            }
            /*
            if (in_array($date, $holidays)) {
                continue; // skip holidays
            }
            */
            if ($holidayService->isDateHoliday($date)) {
                continue; // skip holidays
            }

            foreach ($practitioners as $practitioner_id) {
                if ($vacationService->isDateInVacation($practitioner_id, $date)) {
                    continue; // skip vacation days
                }

                foreach ($this->timeSlots as [$startTime, $endTime]) {
                    //DB::table('available_time_slots_diagnosis')->updateOrInsert(
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