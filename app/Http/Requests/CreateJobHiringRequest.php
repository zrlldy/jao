<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateJobHiringRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],
            'location' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],
            'employment_type' => [
                'required',
                'string',
                Rule::in(['full-time', 'part-time', 'contract', 'internship']),
            ],

            'title' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],

            'description' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],

            'requirements' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],

            'status' => [
                'required',
                'string',
                Rule::in([
                    'draft',
                    'published',
                    'closed',
                ]),
            ],

            'published_at' => [
                'nullable',
                'date',
                'after_or_equal:created_at',
            ],

            'closed_at' => [
                'nullable',
                'date',
                'after:published_at',
            ],
        ];
    }
}
