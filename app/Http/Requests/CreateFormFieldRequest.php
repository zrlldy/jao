<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use function Pest\Laravel\json;

class CreateFormFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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

            'placeholder' => ['nullable', 'string'],
            'default_value' => ['nullable'],

            'is_required' => ['sometimes', 'boolean'],

            'settings' => ['nullable', 'array'],
            'validation_rules' => ['nullable', 'array'],
        ];
    }
}
