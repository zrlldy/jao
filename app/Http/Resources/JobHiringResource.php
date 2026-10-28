<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobHiringResource extends JsonResource
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
            'department' => $this->department,
            'location' => $this->location,
            'employmentType' => $this->employment_type,

            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'closed_at' => $this->closed_at,
        ];
    }
}
