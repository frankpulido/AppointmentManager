<?php
declare(strict_types=1);
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// The command is scheduled to run daily at midnight and clean useless past available slots.
// Check /app/Console/Commands/CleanPastAvailableSlots.php
Schedule::command('slots:clean-past')->dailyAt('00:01')->runInBackground();

// The command is scheduled to run yearly on Jan 1st at 00:05. Seeds Holidays for year+2 and deletes past holidays.
// Check /app/Console/Commands/RefreshHolidays.php
Schedule::command('app:refresh-holidays')->yearlyOn(1, 1, '00:05')->runInBackground();