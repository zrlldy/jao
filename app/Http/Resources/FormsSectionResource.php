<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormsSectionResource extends JsonResource
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

            'form_version' => $this->whenLoaded('formVersion', function () {
                return [
                    'id' => $this->formVersion->id,
                    'version' => $this->formVersion->version,
                    'status' => $this->formVersion->status,
                    'published_at' => $this->formVersion->published_at,
                ];
            }),
            'title' => $this->title,
            'description' => $this->description,
            'sort_order' => $this->sort_order,
        ];
    }
}
