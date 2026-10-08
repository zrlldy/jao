<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
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
            'applicant_no' => $this->applicant_no,
            'application_invitation' => ApplicationInvitationResource::make($this->whenLoaded('applicationInvitation')),
            'job_hiring' => JobHiringResource::make($this->whenLoaded('jobHiring')),
            'form_version' => FormVersionResource::make($this->whenLoaded('formVersion')),
            'status' => $this->status,
            'submitted_at' => $this->submitted_at,
            'reviewed_by' => $this->reviewed_by,
            'deleted_at' => $this->whenNotNull($this->deleted_at),
        ];
    }
}
