<?php
declare(strict_types=1);
namespace App\Services;

use Carbon\CarbonInterface;

class IsHolidayService
{
    public function isDateHoliday(CarbonInterface $date): bool
    {
        $year = $date->year;
        $holidays = $this->holidaysForYear($year);
        return in_array($date->toDateString(), array_values($holidays), true);
    }

    public function holidaysForYear(int $year): array
    {
        $easter = easter_date($year);
        $holidays = [
            'Año Nuevo' => "$year-01-01",
            'Reyes Magos' => "$year-01-06",
            'Domingo de Ramos' => date('Y-m-d', strtotime('-7 days', $easter)),
            'Viernes Santo' => date('Y-m-d', strtotime('-2 days', $easter)),
            'Domingo de Pascua' => date('Y-m-d', $easter),
            'Lunes de Pascua' => date('Y-m-d', strtotime('+1 day', $easter)),
            'Fiesta del Trabajo' => "$year-05-01",
            'Segunda Pascua' => date('Y-m-d', strtotime('+50 days', $easter)),
            'San Juan' => "$year-06-24",
            'Asunción de la Virgen' => "$year-08-15",
            'Diada de Catalunya' => "$year-09-11",
            'Mare de Déu de la Mercè' => "$year-09-24",
            'Todos los Santos' => "$year-11-01",
            'Día de la Constitución' => "$year-12-06",
            'Inmaculada Concepción' => "$year-12-08",
            'Navidad' => "$year-12-25",
            'Nochevieja' => "$year-12-31",
        ];
        return $holidays;
    }
}