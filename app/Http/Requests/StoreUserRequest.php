<?php
declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
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

    protected function prepareForValidation()
    {
        $this->merge([
            'role' => $this->input('role', User::DEFAULT_ROLE),
        ]);
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
            'role' => 'string|in:' . implode(',', User::VALID_ROLES),
            // Password is not required when superadmin creates a user, user will set it later
            'password' => 'sometimes|string|min:8',
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
            // Password is not required when superadmin creates a user, user will set it later
            //'password.required' => 'La contraseña es un campo obligatorio',
            //'password.string' => 'La contraseña debe ser una cadena de texto',
            //'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'role.string' => 'El rol debe ser una cadena de texto',
            'role.in' => 'El rol debe ser uno de los siguientes : ' . implode(', ', User::VALID_ROLES),
        ];
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'No autorizado para realizar esta acción'
            ], 403)
        );
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => $validator->errors()->first()
            ], 422)
        );
    }
}