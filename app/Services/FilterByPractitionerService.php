<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\Practitioner;

class FilterByPractitionerService
{
    public function filterByPractitionerId(array $array)
    {
        $practitioners = Practitioner::get()->mapWithKeys(function($p) {
            return [$p->id => $p->first_name . ' ' . $p->last_name];
        })->toArray();

        $arrayFiltered = [];

        foreach($array as $item) {
            $arrayFiltered[$item->practitioner_id][] = $item;
        }

        return response()->json([
            'practitioners' => $practitioners,
            'arrayFiltered' =>  $arrayFiltered],
            200
        );
    }
}