<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Practitioner;
use App\Http\Requests\StorePractitionerRequest;
use App\Services\PractitionerCreationService;
use App\Exceptions\PractitionerCreationException;
use Throwable;

/**
 * @OA\Tag(
 *     name="Admin",
 *     description="Administrative Endpoints for Managing Users and Practitioners"
 * )
 */
class AdminController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin",
     *     tags={"Admin"},
     *     summary="Get all users and practitioners (Admin only)",
     *     description="Retrieve a list of all users and practitioners. Accessible only by admin and superadmin roles.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of users and practitioners",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="users",
     *                 type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="BxL2B@example.com"),
     *                     @OA\Property(property="role", type="string", example="admin"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                 ),
     *             ),
     *             @OA\Property(
     *                 property="practitioners",
     *                 type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="BxL2B@example.com"),
     *                     @OA\Property(property="role", type="string", example="practitioner"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized access",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No autorizado para realizar esta acción")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $user = auth('sanctum')->user();

        if ($user->role === 'admin' || $user->role === 'superadmin') {
            // Admin can see all users and practitioners
            $users = User::all();
            $practitioners = Practitioner::all();
            return response()->json([
                'users' => $users,
                'practitioners' => $practitioners,
            ], 200);
        } else {
            return response()->json([
                'message' => 'No autorizado para realizar esta acción'
            ], 403);
        }
    }

    public function createPractitioner(Request $request)
    {
        // Logic to show form for creating a new practitioner
    }

    /**
     * @OA\Post(
     *     path="/admin/practitioners",
     *     tags={"Admin"},
     *     summary="Create a new practitioner (Admin only)",
     *     description="Create a new practitioner in the system. Accessible only by admin and superadmin roles.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Dr. Jane Smith"),
     *             @OA\Property(property="email", type="string", example="BxL2B@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="role", type="string", example="practitioner"),
     *             @OA\Property(property="phone", type="string", example="123456789"),
     *             @OA\Property(property="country", type="string", example="Spain"),
     *             @OA\Property(property="city", type="string", example="Madrid"),
     *             @OA\Property(property="postal_code", type="string", example="28001"),
     *             @OA\Property(property="address", type="string", example="Calle Mayor, 1"),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Practitioner created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Profesional creado con éxito"),
     *             @OA\Property(property="practitioner", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Dr. Jane Smith"),
     *                 @OA\Property(property="email", type="string", example="BxL2B@example.com"),
     *                 @OA\Property(property="role", type="string", example="practitioner"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function storePractitioner(StorePractitionerRequest $request)
    {
        // Logic to store a newly created practitioner in storage
        $validated = $request->validated();
        // Add country code (Spain only) to practitioner phone number
        $validated['phone'] = '+34' . $validated['phone'];

        try {
            $practitionerService = new PractitionerCreationService();
            $newPractitioner = $practitionerService->create($validated);
            
        } catch (PractitionerCreationException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(
            [
                'message' => 'Profesional creado con éxito',
                'practitioner' => $newPractitioner
            ], 201);
    }
}