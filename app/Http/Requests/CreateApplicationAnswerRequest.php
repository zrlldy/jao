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
            'form_version_id' => ['required', 'uuid'],
            'answers' => ['present', 'array', 'max:100'],
            'answers.*' => ['required', 'array:form_field_id,value'],
            'answers.*.form_field_id' => ['required', 'uuid', 'distinct:strict'],
            'answers.*.value' => ['present', 'nullable', 'string', 'max:10000'],
        ];
    }
}
