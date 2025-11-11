<?php
declare(strict_types=1);
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AvailableTimeSlot;
use App\Models\Appointment;
use App\Services\AppointmentCreationService;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all available slots within the 91-day window
        $availableSlots = AvailableTimeSlot::whereDate('slot_date', '>=', now())
            ->whereDate('slot_date', '<=', now()->addDays(91))
            ->orderBy('practitioner_id')
            ->orderBy('slot_date')
            ->orderBy('slot_start_time')
            ->get();

        $now = now();
        $appointmentCreationService = new AppointmentCreationService();

        foreach ($availableSlots as $slot) {
            // Determine if we should book this slot based on the time period
            $weeksAhead = $now->diffInWeeks($slot->slot_date);
            $bookSlot = match(true) {
                $weeksAhead < 2 => true,           // 100% for first 2 weeks
                $weeksAhead < 4 => rand(1, 10) <= 9, // 90% for next 2 weeks
                $weeksAhead < 13 => rand(1, 5) <= 4, // 80% for remaining weeks
                default => false
            };

            if ($bookSlot) {
                Appointment::create([
                    'practitioner_id' => $slot->practitioner_id,
                    'appointment_date' => $slot->slot_date->format('Y-m-d'),
                    'appointment_start_time' => $slot->slot_start_time,
                    'appointment_end_time' => $slot->slot_end_time,
                    'kind_of_appointment' => 'treatment',
                    'patient_first_name' => 'Test',
                    'patient_last_name' => 'Patient ' . rand(1, 1000),
                    'patient_email' => 'test' . rand(1, 1000) . '@example.com',
                    'patient_phone' => '+34' . rand(600000000, 699999999),
                ]);
            }
        }
    }
}