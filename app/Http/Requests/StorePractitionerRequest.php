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
            'email' => 'required|string',
            'phone' => 'required|integer',
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
            'email.string' => 'El email debe tener un formato válido',
            'phone.required' => 'El teléfono es un campo obligatorio',
            'phone.integer' => 'El teléfono sólo puede contener números',
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