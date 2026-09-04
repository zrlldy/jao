<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateFormVersionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $header = config('services.api.header');
        $expectedApiKey = config('services.jao.api_key');
        $apiKey = $this->header($header);
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
            'form_template_id' => ['uuid', 'unique', 'required'],
            'version' => ['required', 'string', 'min:3', 'max:100'],
            'status' => ['required', 'string', 'min:3', 'max:100'],
            'published_at' => ['date', 'required']
        ];
    }
}
