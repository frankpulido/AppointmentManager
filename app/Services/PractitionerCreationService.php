<?php
declare(strict_types=1);
namespace App\Services;
// This Service creates new Practitioners
use App\Models\Practitioner;
//use Illuminate\Support\Facades\DB;
use Throwable;
use App\Exceptions\PractitionerCreationException;

class PractitionerCreationService
{
    public function create(array $newData)
    {
        try {
            $newPractitioner = Practitioner::create($newData);
            return $newPractitioner;
        } catch (Throwable $e) {
            throw new PractitionerCreationException('Error creating Practitioner');
        }
        // Code below was the option in case a pibot table was used and specialties were an array of IDs
        // but we are using a JSON column in practitioners table instead
        /*
        try {
            DB::transaction(function () use ($newData) {
                $newPractitioner = Practitioner::create($newData);
                $newPractitioner->specialties()->sync($newData['specialties']); // sync() expects array of IDs with using pivot table
                return $newPractitioner;
            });
        } catch (Throwable $e) {
            throw new PractitionerCreationException('Error creating Practitioner');
        }
        */
    }
}