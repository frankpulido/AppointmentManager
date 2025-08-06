<?php
declare(strict_types=1);
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Holiday;
use App\Models\Vacation;
use App\Models\AvailableTimeSlot;

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
        $holidays = Holiday::pluck('date')->toArray();
        $vacations = Vacation::pluck('date')->toArray();

        $start = Carbon::today();
        $end = Carbon::create($start->year + 1, 12, 31);

        for ($date = $start; $date->lte($end); $date->addDay()) {
            if ($date->isWeekend()) {
                continue; // skip Saturday and Sunday
            }

            if (in_array($date, $holidays) || in_array($date, $vacations)) {
                continue; // skip holidays and vacations
            }

            foreach ($this->timeSlots as [$startTime, $endTime]) {
                DB::table('available_time_slots_diagnosis')->updateOrInsert(
                    [
                        'date' => $date,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    ]
                );
            }
        }
    }
}
