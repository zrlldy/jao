<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpateJobHiringRequest extends FormRequest
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
            'department' => ['string', 'sometimes', 'max:100', 'min:3'],
            'location' => ['string', 'sometimes', 'max:100', 'min:3'],
            'employment_type' => ['sometimes', 'string', Rule::in(['full-time', 'part-time', 'contract', 'internship'])],
            'title' => ['string', 'sometimes', 'max:100', 'min:3'],
            'description' => ['string', 'sometimes', 'max:100', 'min:10'],
            'requirements' => ['string', 'sometimes', 'max:100', 'min:10'],
            'status' => ['string', 'sometimes', 'min:3', 'max:100'],
            'published_at' => ['date', 'sometimes', 'after_or_equal:created_at'],
            'closed_at' => ['date', 'sometimes', 'after:published_at'],
        ];
    }
}
