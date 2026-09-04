<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpateJobHiringRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $header = config('services.api.header');
        $expectedApiKey = config('services.jao.api_key');
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
            'department_id' => "uuid|sometimes",
            'location_id' => "uuid|sometimes",
            'employment_type_id' => "uuid|sometimes",
            "title" => "string|sometimes|max:20|min:3",
            "slug" => "string|sometimes|max:100|min:5",
            "description" => "string|sometimes|max:100|min:10",
            "requirements" => "string|sometimes|max:100|min:10",
            "status" => "string|sometimes|min:3|max:20",
            "published_at" => "date|sometimes",
            "closed_at" => "date|sometimes"
        ];
    }
}
