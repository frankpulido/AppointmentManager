<?php
declare(strict_types=1);
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Holiday;
use App\Services\IsHolidayService;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = now()->year;
        $holidayService = new IsHolidayService();
        $holidays1 = $holidayService->holidaysForYear($year);
        $holidays2 = $holidayService->holidaysForYear($year + 1);
        $holidays = array_merge($holidays1, $holidays2);
        
        foreach ($holidays as $title => $date) {
            Holiday::UpdateOrCreate(
                [
                    'name' => $title,
                    'date'  => $date,
                ]
            );
        }
    }
}