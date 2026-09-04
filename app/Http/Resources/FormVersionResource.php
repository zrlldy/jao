<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class FormVersionResource extends JsonResource
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
            'form_template' => $this->whenLoaded('formTemplate', fn() => [
                'id' => $this->formTemplate->id,
                'name' => $this->formTemplate->name,
            ]),
            'version' => $this->version,
            'status' => $this->status,
            'published_at' => $this->published_at,
        ];
    }
}
