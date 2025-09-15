<?php
declare(strict_types=1);
namespace App\Console\Commands;
// This command deletes past available time slots. Scheduled to run daily at midnight, check /routes/console.php.
// In the shell, run: php artisan slots:clean-past

use Illuminate\Console\Command;
use App\Models\AvailableTimeSlot;
use App\Models\AvailableTimeSlotDiagnosis;
use Illuminate\Support\Facades\Log;

class CleanPastAvailableSlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slots:clean-past';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete available time slots from past dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deletedTreatment = AvailableTimeSlot::whereDate('slot_date', '<', now()->toDateString())->delete();
        $deletedDiagnosis = AvailableTimeSlotDiagnosis::whereDate('slot_date', '<', now()->toDateString())->delete();
        
        $total = $deletedTreatment + $deletedDiagnosis;

        $this->info("Cleaned {$deletedTreatment} past treatment slots and {$deletedDiagnosis} past diagnosis slots");
        Log::info("Daily slot cleanup: {$total} records deleted ({$deletedTreatment} treatment, {$deletedDiagnosis} diagnosis)");
    }
}