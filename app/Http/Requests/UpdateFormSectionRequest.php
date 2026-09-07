<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFormSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        $header = config('services.api.header');
        $apiKey = $this->header($header);
        $expectedApiKey = config('services.jao.api_key');

        if (!is_string($apiKey) || !is_string($expectedApiKey)) {
            return false;
        }

        return hash_equals($expectedApiKey, $apiKey);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'form_template_id' => ['sometimes', 'exists:form_templates,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'version' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
