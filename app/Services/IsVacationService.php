<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\Vacation;
use Carbon\CarbonInterface;

class IsVacationService
{
    public function isDateInVacation(int $practitionerId, CarbonInterface $date): bool
    {
        return Vacation::where('practitioner_id', $practitionerId)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->exists();
    }
}