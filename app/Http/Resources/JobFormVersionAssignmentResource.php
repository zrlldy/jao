<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobFormVersionAssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'job_hiring' => new JobHiringResource($this->whenLoaded('jobHiring')),
            'form_version' => new FormVersionResource($this->whenLoaded('formVersion')),
        ];
    }
}
