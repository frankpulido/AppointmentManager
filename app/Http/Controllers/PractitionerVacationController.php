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

/**
 * @OA\Tag(
 *     name="PractitionerVacation",
 *     description="Endpoints for Managing Practitioner Vacations"
 * )
 */
class PractitionerVacationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/practitioner-vacations",
     *     tags={"PractitionerVacation"},
     *     summary="Get vacations for practitioners",
     *     description="Retrieve a list of vacations for practitioners. Admins can see all practitioners' vacations, while practitioners can see only their own.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of vacations",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="practitioners",
     *                 type="object",
     *                 description="List of practitioners with their IDs and names",
     *                 example={"1": "John Doe", "2": "Jane Smith"}
     *             ),
     *             @OA\Property(
     *                 property="vacations",
     *                 type="object",
     *                 description="Vacations grouped by practitioner ID",
     *                 example={
     *                     "1": {
     *                         {
     *                             "id": 1,
     *                             "practitioner_id": 1,
     *                             "start_date": "2024-08-01",
     *                             "end_date": "2024-08-10",
     *                             "created_at": "2024-07-01T12:00:00Z",
     *                             "updated_at": "2024-07-01T12:00:00Z"
     *                         }
     *                     },
     *                     "2": {
     *                         {
     *                             "id": 2,
     *                             "practitioner_id": 2,
     *                             "start_date": "2024-09-05",
     *                             "end_date": "2024-09-15",
     *                             "created_at": "2024-07-02T12:00:00Z",
     *                             "updated_at": "2024-07-02T12:00:00Z"
     *                         }
     *                     }
     *                 }
     *             )
     *         )
     *     )
     * )
    */
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

    /**
     * @OA\Post(
     *     path="/practitioner-vacations",
     *     tags={"PractitionerVacation"},
     *     summary="Create a new vacation period for a practitioner",
     *     description="Create a new vacation period for a practitioner.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"practitioner_id","start_date","end_date"},
     *             @OA\Property(property="practitioner_id", type="integer", example=1),
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-08-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2024-08-10"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Vacation created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El período de vacaciones se ha creado con éxito"),
     *             @OA\Property(property="vacation", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="practitioner_id", type="integer", example=1),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2024-08-01"),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2024-08-10"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-01T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-01T12:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to create vacation")
     *         )
     *     )
     * )
    */
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

    /**
     * @OA\Delete(
     *     path="/practitioner-vacations",
     *     tags={"PractitionerVacation"},
     *     summary="Delete a vacation period for a practitioner",
     *     description="Delete a vacation period for a practitioner.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"practitioner_id","vacation_id"},
     *             @OA\Property(property="practitioner_id", type="integer", example=1),
     *             @OA\Property(property="vacation_id", type="integer", example=1),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vacation deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El período de vacaciones se ha eliminado con éxito")
     *         )
     *     )
     * )
    */
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

    /**
     * @OA\Put(
     *     path="/practitioner-vacations",
     *     tags={"PractitionerVacation"},
     *     summary="Update a vacation period for a practitioner",
     *     description="Update a vacation period for a practitioner.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"practitioner_id","vacation_id","start_date","end_date"},
     *             @OA\Property(property="practitioner_id", type="integer", example=1),
     *             @OA\Property(property="vacation_id", type="integer", example=1),
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-08-05"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2024-08-15"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vacation updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El período de vacaciones se ha actualizado con éxito"),
     *             @OA\Property(property="vacation", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="practitioner_id", type="integer", example=1),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2024-08-05"),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2024-08-15"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-01T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-10T12:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vacation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Vacation not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to update vacation")
     *         )
     *     )
     * )
    */
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