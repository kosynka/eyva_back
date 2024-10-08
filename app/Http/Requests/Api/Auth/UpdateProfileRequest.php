<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'name' => ['string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'photo' => ['nullable', 'mimes:png,jpg,jpeg,gif'],
        ];
    }

    public function messages(): array
    {
        return [
            'birth_date.date' => 'Необходимо указать корректную дату рождения.',
        ];
    }
}
