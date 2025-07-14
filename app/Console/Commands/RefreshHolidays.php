<?php
declare(strict_types=1);
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Database\Seeders\HolidaySeeder;

class RefreshHolidays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-holidays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed holidays for year+2 and delete past holidays (year-1 and earlier)';

    /**
     * Execute the console command.
     */
    public function handle() : int
    {
        $now = now();
        $currentYear = $now->year;
        $targetYear = $currentYear + 2;
        $cutoffDate = $currentYear - 1;

        $this->call(HolidaySeeder::class, ['--year' => $targetYear]);

        DB::table('holidays')
            ->where('date', '<', "{$cutoffDate}-01-01")
            ->delete();
        $this->info("Seeded holidays for {$targetYear} and cleaned up data before {$cutoffDate}.");
        return Command::SUCCESS;
    }
}