<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationAnswerResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'applicant' => $this->whenLoaded('application', function () {
                return [
                    'application_id' => $this->application->id,
                    'application_no' => $this->application->application_no,
                ];
            }),
            'form_field' => FormFieldResource::make($this->whenLoaded('formField')),
            'value' => $this->value,
        ];
    }
}
