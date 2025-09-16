<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vacation;
use App\Models\Practitioner;
use App\Http\Requests\StoreVacationRequest;
use App\Http\Requests\DeleteVacationRequest;
use App\Http\Requests\UpdateVacationRequest;
use Illuminate\Support\Facades\Log;
use Throwable;

class PractitionerVacationController extends Controller
{
    public function index()
    {
        $user = auth('sanctum')->user();

        if ($user->role === 'admin') {
            $practitioners = Practitioner::get()->mapWithKeys(function($p) {
                return [$p->id => $p->first_name . ' ' . $p->last_name];
            })->toArray();

            $vacations = Vacation::query()
                ->orderBy('practitioner_id')
                ->orderBy('start_date')
                ->get()
                ->groupBy('practitioner_id')
                ->toArray();

        } else {
            $practitioners = Practitioner::where('id', $user->practitioner_id)
                ->get()
                ->mapWithKeys(function($p) {
                    return [$p->id => $p->first_name . ' ' . $p->last_name];
                })->toArray();
        }

        $vacations = Vacation::where('practitioner_id', $user->practitioner_id)
                ->orderBy('start_date')
                ->get()
                ->groupBy('practitioner_id')
                ->toArray();

        return response()->json([
            'practitioners' => $practitioners,
            'vacations' => $vacations,
        ], 200);
    }

    public function store(StoreVacationRequest $request)
    {
        $validated = $request->validated();
        
        if($validated) {
            try {
                $vacation = Vacation::create($validated);
                return response()->json([
                    'message' => "El período de vacaciones se ha creado con éxito",
                    'vacation' => $vacation,
                ], 201);
            } catch (Throwable $e) {
                Log::error('Failed to create or update vacation: ' . $e->getMessage(), [
                    'practitioner_id' => $validated['practitioner_id'],
                ]);
                return response()->json(['message' => 'Failed to create vacation'], 500);
            }
        }
    }

    public function destroy(DeleteVacationRequest $request)
    {
        $validated = $request->validated();
        $vacation = Vacation::where('id', $validated['vacation_id'])
            ->where('practitioner_id', $validated['practitioner_id'])
            ->first();
        if($vacation) {
            $vacation->delete();
        }
        return response()->json([
            'message' => "El período de vacaciones se ha eliminado con éxito",
        ], 200);
    }

    public function update(UpdateVacationRequest $request)
    {
        $validated = $request->validated();
        $vacation = Vacation::where('id', $validated['vacation_id'])
            ->where('practitioner_id', $validated['practitioner_id'])
            ->first();
        if($vacation) {

            try {
            $vacation->delete();
            $newVacation = Vacation::create($validated);

            return response()->json([
                'message' => "El período de vacaciones se ha actualizado con éxito",
                'vacation' => $newVacation,
            ], 200);

            } catch (Throwable $e) {
                Log::error('Failed to update vacation: ' . $e->getMessage(), [
                    'practitioner_id' => $validated['practitioner_id'],
                ]);
                return response()->json(['message' => 'Failed to update vacation'], 500);
            }
        }

        return response()->json(['message' => 'Vacation not found'], 404);
    }
}