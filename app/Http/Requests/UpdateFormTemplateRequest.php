<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFormTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $expectedApiKey = config('services.jao.api_key');

        $header = config("services.api.header");
        $apiKey = $this->header($header);

        if (!is_string($expectedApiKey) || !is_string($apiKey)) {
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
            "name" => ['required', 'min:3', 'max:20', 'unique'],
            "description" => ['required', 'min:3', 'max:100', 'unique'],
            "is_active" => ['required', 'boolean'],
        ];
    }
}
