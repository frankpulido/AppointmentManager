<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;
use App\Exceptions\UserCreationException;

class UserCreationService
{
    /**
     * Create a new user with the given data.
     *
     * @param array $data
     * @return User
     * @throws UserCreationException
     */
    public function createUser(array $data): User
    {
        DB::beginTransaction();

        try {
            $newUser = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'practitioner_id' => null,
            ]);

            DB::commit();
            return $newUser;
        } catch (Throwable $e) {
            DB::rollBack();
            throw new UserCreationException('Failed to create user: ' . $e->getMessage(), 0, $e);
        }
    }
}