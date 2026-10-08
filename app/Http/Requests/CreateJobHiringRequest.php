<?php

namespace App\Http\Requests;

use App\Models\Department;
use App\Models\EmploymentType;
use App\Models\JobHiring;
use App\Models\Location;
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
            'department_id' => [
                'required',
                'uuid',
                'exists:departments,id',
                Rule::exists(Department::class, 'id'),
            ],

            'location_id' => [
                'required',
                'uuid',
                'exists:locations,id',
                Rule::exists(Location::class, 'id')
            ],

            'employment_type_id' => [
                'required',
                'uuid',
                'exists:employment_types,id',
                Rule::exists(EmploymentType::class, 'id')
            ],

            'title' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],

            'slug' => [
                'required',
                'string',
                'min:3',
                'max:150',
                'alpha_dash',
                'unique:job_hirings,slug',
                Rule::unique(JobHiring::class, 'slug')
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
                'after_or_equal:created_at'
            ],

            'closed_at' => [
                'nullable',
                'date',
                'after:published_at',
            ],
        ];
    }
}
