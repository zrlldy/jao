<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateFormFieldRequest extends FormRequest
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
            'key' => ['required', 'string', 'max:255'],
            'label' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'placeholder' => ['nullable', 'string', 'max:255', 'min:5'],
            'default_value' => ['nullable', 'min:0', 'max:255'],
            'is_required' => ['sometimes', 'boolean'],
            'settings' => ['nullable', 'array'],
            'validation_rules' => ['nullable', 'array'],
        ];
    }
}
