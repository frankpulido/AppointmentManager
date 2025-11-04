<?php
declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Appointment;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAppointmentRequest extends FormRequest
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
            'appointment_date' => 'required|date',
            'appointment_start_time' => 'required|date_format:H:i:s',
            //'appointment_end_time' => 'required|date_format:H:i:s|after:appointment_start_time',
            'patient_first_name' => 'required|string|max:30|regex:/^[a-zA-Z\s]+$/',
            'patient_last_name'=> 'required|string|max:30|regex:/^[a-zA-Z\s]+$/',
            'patient_email' => 'email|max:50|regex:/^[^@]+@[^@]+\.[^@]+$/',
            'patient_phone' => [
                'required',
                'string',
                'max:16',
                'regex:/^\+[1-9]\d{1,14}$/'
            ],
            //'patient_phone'=> 'required|string|max:15|regex:/^\+?[0-9\s\-]+$/',
            'kind_of_appointment' => 'required|in:' . implode(',', Appointment::VALID_KINDS),
            'status' => 'in:' . implode(',', Appointment::VALID_STATUSES),
        ];
    }

    public function messages(): array
    {
        return [
            'practitioner_id.required' => 'El id del profesional es un campo obligatorio',
            'practitioner_id.exists' => 'El profesional indicado no existe',
            'appointment_date.required' => 'La fecha de la cita es un campo obligatorio',
            'appointment_date.date' => 'La fecha de la cita debe tener un formato de fecha valido (AAAA-MM-DD)',
            'appointment_start_time.required' => 'La hora de inicio de la cita es un campo obligatorio',
            'appointment_start_time.date_format' => 'La hora de inicio de la cita debe tener un formato de hora valido (HH:MM:SS)',
            'patient_first_name.required' => 'El nombre del paciente es un campo obligatorio',
            'patient_first_name.string' => 'El nombre del paciente debe ser una cadena de texto',
            'patient_first_name.max' => 'El nombre del paciente debe tener un máximo de 30 caracteres',
            'patient_first_name.regex' => 'El nombre del paciente debe contener solo letras y espacios',
            'patient_last_name.required' => 'El apellido del paciente es un campo obligatorio',
            'patient_last_name.string' => 'El apellido del paciente debe ser una cadena de texto',
            'patient_last_name.max' => 'El apellido del paciente debe tener un.maxcdn de 30 caracteres',
            'patient_last_name.regex' => 'El apellido del paciente debe contener solo letras y espacios',
            'patient_email.email' => 'El correo del paciente debe tener un formato de correo valido',
            'patient_email.max' => 'El correo del paciente debe contener un máximo de 50 caracteres',
            'patient_phone.required' => 'El teléfono del paciente es un campo obligatorio',
            'patient_phone.string' => 'El teléfono del paciente debe ser una cadena de texto',
            'patient_phone.max' => 'El teléfono del paciente debe contener un máximo de 16 caracteres',
            'patient_phone.regex' => "El teléfono del paciente debe comenzar con '+' y contener sólo números a continuación",
            'kind_of_appointment.required' => 'El tipo de visita es un campo obligatorio',
            'kind_of_appointment.in' => 'El tipo de visita debe ser ' . implode(', ', Appointment::VALID_KINDS),
            'status.in' => 'El estado de la cita debe ser ' . implode(', ', Appointment::VALID_STATUSES),
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