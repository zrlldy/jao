<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldOptionResource extends JsonResource
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
            'form_field' => $this->whenLoaded('formField', function () {
                return [
                    'id' => $this->formField->id,
                    'name' => $this->formField->name,
                    'type' => $this->formField->type,
                ];
            }),
            'value' => $this->value,
            'label' => $this->label,
            'sort_order' => $this->sort_order,
        ];
    }
}
