<?php
declare (strict_types= 1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SeedAvailableSlotsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth('sanctum')->user();
        // Admin can create appointments for anyone
        if ($user->role === 'admin') {
            return true;
        }
        
        // Practitioners can only create appointments for themselves
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
            'slots_start_date' => 'required|date|date_format:Y-m-d',
            'slots_end_date' => 'date|date_format:Y-m-d|after_or_equal:slots_start_date'
        ];
    }

    public function messages(): array
    {
        return [
            'practitioner_id.required' => 'El id del profesional es un campo obligatorio',
            'practitioner_id.exists' => 'El profesional indicado no existe',
            'slots_start_date.required' => 'La fecha de inicio es un campo obligatorio',
            'slots_start_date.date' => 'La fecha debe tener un formato válido (AAAA-MM-DD)',
            'slots_end_date.date' => 'La fecha debe tener un formato válido (AAAA-MM-DD)',
            'slots_end_date.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio',
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