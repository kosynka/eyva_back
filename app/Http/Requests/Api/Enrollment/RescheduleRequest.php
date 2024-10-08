<?php

namespace App\Http\Requests\Api\Enrollment;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleRequest extends FormRequest
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
        ];
    }
}