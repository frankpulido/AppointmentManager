<?php
declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Appointment;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAvailableSlotRequest extends FormRequest

{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'slot_date' => 'required|date',
            'slot_start_time' => 'required|date_format:H:i:s',
            'slot_end_time' => 'required|date_format:H:i:s|after:slot_start_time',
            'kind_of_appointment' => 'required|in:' . implode(',', Appointment::VALID_KINDS)
        ];
    }

    public function messages(): array
    {
        return [
            'practitioner_id.required' => 'El id del profesional es un campo obligatorio',
            'practitioner_id.exists' => 'El profesional indicado no existe',
            'slot_date.required' => 'La fecha de la hora de visita es un campo obligatorio',
            'slot_date.date' => 'La fecha de la hora de visita debe tener un formato de fecha válido (AAAA-MM-DD)',
            'slot_start_time.required' => 'La hora de inicio de la hora de visita es un campo obligatorio',
            'slot_start_time.date_format' => 'La hora de inicio de la hora de visita debe tener un formato de hora válido (HH:MM:SS)',
            'slot_end_time.required' => 'La hora de fin de la hora de visita es un campo obligatorio',
            'slot_end_time.date_format' => 'La hora de fin de la hora de visita debe tener un formato de hora válido (HH:MM:SS)',
            'slot_end_time.after' => 'La hora de fin de la hora de visita debe ser posterior a la hora de inicio',
            'kind_of_appointment.in' => 'El tipo de visita debe ser "diagnose" o "treatment"',
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