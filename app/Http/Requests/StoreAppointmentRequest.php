<?php
declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Appointment;

class StoreAppointmentRequest extends FormRequest
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
            'appointment_date' => 'required|date',
            'appointment_start_time' => 'required|date_format:H:i:s',
            'appointment_end_time' => 'required|date_format:H:i:s|after:appointment_start_time',
            'patient_first_name' => 'required|string|max:30|regex:/^[a-zA-Z\s]+$/',
            'patient_last_name'=> 'required|string|max:30|regex:/^[a-zA-Z\s]+$/',
            'patient_email' => 'email|max:50',
            'patient_phone'=> 'required|string|max:15|regex:/^\+?[0-9\s\-]+$/',
            'kind_of_appointment' => 'required|in:' . implode(',', Appointment::VALID_KINDS),
            'status' => 'in:' . implode(',', Appointment::VALID_STATUSES),
        ];
    }
}