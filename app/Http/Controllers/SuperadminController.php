<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserCreationService;
use App\Exceptions\UserCreationException;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;

/**
 * @OA\Tag(
 *     name="Superadmin",
 *     description="Superadmin Endpoints for Managing Users"
 * )
 */
class SuperadminController extends Controller
{
    /**
     * @OA\Post(
     *     path="/superadmin/users",
     *     tags={"Superadmin"},
     *     summary="Create a new user (Superadmin only)",
     *     description="Endpoint to create a new user. Accessible only by superadmin role.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","role"},
     *             @OA\Property(property="name", type="string", example="Jane Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="bBz7o@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="role", type="string", example="superadmin"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuario creado con éxito"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Jane Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="bBz7o@example.com"),
     *                 @OA\Property(property="role", type="string", example="superadmin"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-01T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-01T12:00:00Z"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function storeUser(StoreUserRequest $request)
    {
        $validated = $request->validated();
        // Logic to store a newly created user in storage
        try {
            $createUserService = new UserCreationService();
            $newUser = $createUserService->createUser($validated);
        } catch (UserCreationException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(
            [
                'message' => 'Usuario creado con éxito',
                'user' => $newUser
            ], 201);
    }
}
