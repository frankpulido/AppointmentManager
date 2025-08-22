<?php
declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Appointment;

class DeleteAvailableSlotRequest extends FormRequest
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
            'kind_of_appointment' => 'required|in:' . implode(',', Appointment::VALID_KINDS),
            'slot_id' => 'required|integer',
            'practitioner_id' => 'required|exists:practitioners,id',
        ];
    }
}
