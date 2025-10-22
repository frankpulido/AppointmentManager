<?php
declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePractitionerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth('sanctum')->user();
        // ONLY Admins can create Practitioners
        if ($user->role === 'admin' || $user->role === 'superadmin') {
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'specialties' => 'array',
            'specialties.*' => 'exists:specialties,specialty_name',
            'email' => 'required|email|regex:/^[^@]+@[^@]+\.[^@]+$/',
            'phone' => 'required|string|regex:/^[6-9]\d{8}$/', // Spanish phone number format (9 digits total, starting with 6,7,8 or 9)
            'user_id' => 'required|integer|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'El nombre es un campo obligatorio',
            'first_name.string' => 'El nombre debe ser una cadena de texto',
            'last_name.required' => 'El apellido es un campo obligatorio',
            'last_name.string' => 'El apellido debe ser una cadena de texto',
            'specialties.array' => 'Las especialidades deben ser un array de textos',
            'specialties.*.exists' => 'Alguna de las especialidades seleccionadas no es válida',
            'email.required' => 'El email es un campo obligatorio',
            'email.email' => 'El email debe tener un formato válido',
            'phone.required' => 'El teléfono del especialista es un campo obligatorio',
            'phone.string' => 'El teléfono del especialistadebe ser una cadena de texto',
            'phone.regex' => 'El teléfono del especialista debe contener solo números y tener 9 dígitos, comenzando con 6, 7, 8 o 9',
            'user_id.required' => 'El ID de usuario es un campo obligatorio',
            'user_id.integer' => 'El ID de usuario debe ser un número entero',
            'user_id.exists' => 'El usuario seleccionado no existe',
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