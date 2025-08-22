<?php
declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Appointment;

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
}