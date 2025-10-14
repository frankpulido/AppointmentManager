<?php
declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth('sanctum')->user();
        // ONLY Superadmins can create Users
        if ($user->role === 'superadmin') {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email|regex:/^[^@]+@[^@]+\.[^@]+$/',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:' . implode(',', User::VALID_ROLES),
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'El nombre de usuario es un campo obligatorio',
            'username.string' => 'El nombre de usuario debe ser una cadena de texto',
            'username.unique' => 'El nombre de usuario ya existe',
            'email.required' => 'El email es un campo obligatorio',
            'email.email' => 'El email debe tener un formato de correo valido',
            'email.unique' => 'El email ya existe',
            'email.regex' => 'El email debe tener un formato de correo valido',
            'password.required' => 'La contraseña es un campo obligatorio',
            'password.string' => 'La contraseña debe ser una cadena de texto',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'role.required' => 'El rol es un campo obligatorio',
            'role.string' => 'El rol debe ser una cadena de texto',
            'role.in' => 'El rol debe ser uno de los siguientes : ' . implode(', ', User::VALID_ROLES),
        ];
    }
}
