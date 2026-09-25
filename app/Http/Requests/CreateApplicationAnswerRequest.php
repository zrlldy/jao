<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateApplicationAnswerRequest extends FormRequest
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
            'form_version_id' => ['required', 'uuid', 'exists:form_versions,id'],
            'answer' => ['array', 'required'],
            'answers.*.form_field_id' => [
                'required',
                'uuid',
                'exists:form_fields,id',
            ],
            'answers.*.value' => ['nullable'],
        ];
    }
}
