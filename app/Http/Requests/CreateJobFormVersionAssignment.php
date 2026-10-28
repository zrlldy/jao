<?php

namespace App\Http\Requests;

use App\Models\FormVersion;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateJobFormVersionAssignment extends FormRequest
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
            'form_version_ids' => ['required', 'array', 'min:1'],
            'form_version_ids.*' => ['required', 'uuid', 'distinct', Rule::exists(FormVersion::class, 'id')]
        ];
    }
}
