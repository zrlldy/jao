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
                    'name' => $this->formVersion->name,
                ];
            }),
            'name' => $this->name,
            'version' => $this->version,
            'status' => $this->status,
            'description' => $this->description,
            'is_active' => $this->is_active
        ];
    }
}
