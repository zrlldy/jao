<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFormFieldRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key' => ['sometimes', 'string', 'max:255', 'min:3'],
            'label' => ['sometimes', 'string', 'max:255', 'min:3'],
            'type' => ['sometimes', 'string', 'max:100'],
            'placeholder' => ['nullable', 'string', 'sometimes', 'min:3', 'max:100'],
            'default_value' => ['nullable', 'sometimes', 'min:3', 'max:100'],
            'is_required' => ['sometimes', 'boolean',],
            'settings' => ['sometimes', 'array'],
            'validation_rules' => ['sometimes', 'array'],
        ];
    }
}
