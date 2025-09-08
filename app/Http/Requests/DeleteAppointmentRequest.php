<?php
declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeleteAppointmentRequest extends FormRequest
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
            'appointment_id' => 'required|exists:appointments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'appointment_id.exists' => 'La reserva de visita indicada no existe',
            'practitioner_id.exists' => 'El profesional indicado no existe',
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
            ], 404)
        );
    }
}
