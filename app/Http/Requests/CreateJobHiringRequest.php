<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateJobHiringRequest extends FormRequest
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
            'department_id' => "uuid|required",
            'location_id' => "uuid|required",
            'employment_type_id' => "uuid|required",
            "title" => "string|required|max:20|min:3",
            "slug" => "string|required|max:100|min:5",
            "description" => "string|required|max:100|min:10",
            "requirements" => "string|required|max:100|min:10",
            "status" => "string|required|min:3|max:20",
            "published_at" => "date|required",
            "closed_at" => "date|required"
        ];
    }
}
