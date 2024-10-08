<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'stars' => ['nullable', 'integer', 'between:1,5'],
            'program_id' => ['integer', 'exists:programs,id'],
            'service_id' => ['integer', 'exists:services,id'],
            'schedule_id' => ['integer', 'exists:service_schedules,id'],
            'body' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'stars.between' => 'Не забудьте поставить оценку: от 1 до 5 звёздочек.',
        ];
    }
}
