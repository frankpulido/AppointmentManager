<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserCreationService;
use App\Exceptions\UserCreationException;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;

class SuperadminController extends Controller
{
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
