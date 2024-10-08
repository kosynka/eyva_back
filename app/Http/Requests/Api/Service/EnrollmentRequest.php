<?php

namespace App\Http\Requests\Api\Service;

use Illuminate\Foundation\Http\FormRequest;

class EnrollmentRequest extends FormRequest
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
            'service_schedule_id' => ['required', 'integer', 'exists:service_schedules,id'],
            'user_program_id' => ['nullable', 'integer', 'exists:user_programs,id'],
            'user_abonnement_id' => ['nullable', 'integer', 'exists:user_abonnements,id'],
            'user_abonnement_present_id' => ['nullable', 'integer', 'exists:user_abonnement_presents,id'],
        ];
    }
}
