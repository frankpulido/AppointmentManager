<?php
declare(strict_types=1);
namespace App\Console\Commands;
// This command seeds Holidays on Jan 1st at midnight 2 years ahead.
// This way the system has Holidays seeded for minimum 1 year and maximum 2 years ahead.
// Also cleans past holidays.
// Check /routes/console.php
// In the shell, run: php artisan app:refresh-holidays

use Illuminate\Console\Command;
use Throwable;
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

    public function handle(): int
    {
        try {
            // Seed holidays (seeder handles current + next year automatically)
            $this->call(HolidaySeeder::class);
            $this->info("Holiday seeding completed successfully.");
        } catch (Throwable $e) {
            $this->error("Failed to seed holidays: " . $e->getMessage());
            return Command::FAILURE;
        }
        
        try {
            // Clean past holidays
            DB::table('holidays')
                ->where('date', '<', now()->toDateString())
                ->delete();
            $this->info("Past holiday cleanup completed successfully.");
        } catch (Throwable $e) {
            $this->error("Failed to clean past holidays: " . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}