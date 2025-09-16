<?php
declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreVacationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth('sanctum')->user();
        // Admin can create vacations for anyone
        if ($user->role === 'admin') {
            return true;
        }
        
        // Practitioners can only create vacations for themselves
        return $user->role === 'practitioner' && $user->practitioner_id === $this->input('practitioner_id');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'practitioner_id' => 'required|exists:practitioners,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'practitioner_id.required' => 'El id del profesional es un campo obligatorio',
            'practitioner_id.exists' => 'El profesional indicado no existe',
            'start_date.required' => 'La fecha de inicio de las vacaciones es un campo obligatorio',
            'start_date.date' => 'La fecha de inicio de las vacaciones debe tener un formato de fecha válido (AAAA-MM-DD)',
            'start_date.after_or_equal' => 'La fecha de inicio de las vacaciones no puede ser anterior a hoy',
            'end_date.required' => 'La fecha de fin de las vacaciones es un campo obligatorio',
            'end_date.date' => 'La fecha de fin de las vacaciones debe tener un formato de fecha válido (AAAA-MM-DD)',
            'end_date.after_or_equal' => 'La fecha de fin de las vacaciones no puede ser anterior a la fecha de inicio',
            'reason.string' => 'El motivo debe ser una cadena de texto',
            'reason.max' => 'El motivo no puede exceder los 255 caracteres',
        ];
    }
    
    public function failedAuthorization()
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