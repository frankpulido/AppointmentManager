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

class AdminController extends Controller
{
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