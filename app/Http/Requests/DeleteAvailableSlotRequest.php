<?php
declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Appointment;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeleteAvailableSlotRequest extends FormRequest
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
            'kind_of_appointment' => 'required|in:' . implode(',', Appointment::VALID_KINDS),
            'practitioner_id' => 'required|exists:practitioners,id',
            'slot_id' => 'required|integer',
        ];
    }
        public function messages(): array
    {
        return [
            'kind_of_appointment.in' => 'El tipo de visita debe ser "diagnose" o "treatment"',
            'practitioner_id.exists' => 'El profesional indicado no existe',
            'slot_id.integer' => 'El id de la hora de visita debe ser un entero'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => $validator->errors()->first()
            ], 404)
        );
    }
}
