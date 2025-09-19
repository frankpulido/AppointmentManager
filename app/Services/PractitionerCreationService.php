<?php
declare(strict_types=1);
namespace App\Services;
// This Service creates new Practitioners
use App\Models\Practitioner;
use App\Models\User;
//use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\DB;
use Throwable;
use App\Exceptions\PractitionerCreationException;

class PractitionerCreationService
{
    public function create(array $newData)
    {
        // The User existence is already validated in request, now verify non-linking to another Practitioner
        $user = User::find($newData['user_id']);
        if ($user['practitioner_id'] !== null) {
            throw new PractitionerCreationException('El usuario ya está vinculado a otro profesional');
        }
   
        try {
            DB::beginTransaction();
            $newPractitioner = Practitioner::create($newData); // Create the Practitioner
            $user->practitioner_id = $newPractitioner->id; // Link the new Practitioner to the User
            $user->save();
            DB::commit();
            return $newPractitioner;

        } catch (Throwable $e) {
            DB::rollBack();
            throw new PractitionerCreationException('Error : no se ha podido crear el profesional');
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