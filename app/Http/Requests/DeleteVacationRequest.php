<?php
declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeleteVacationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth('sanctum')->user();
        // Admin can delete vacations for anyone
        if ($user->role === 'admin') {
            return true;
        }
        
        // Practitioners can only delete vacations for themselves
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
            'vacation_id' => 'required|exists:vacations,id',
        ];
    }

    public function messages(): array
    {
        return [
            'practitioner_id.required' => 'El id del profesional es un campo obligatorio',
            'practitioner_id.exists' => 'El profesional indicado no existe',
            'vacation_id.required' => 'El id de las vacaciones es un campo obligatorio',
            'vacation_id.exists' => 'Las vacaciones indicadas no existen',
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