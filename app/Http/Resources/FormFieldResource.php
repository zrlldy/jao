<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldResource extends JsonResource
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
            'key' => $this->key,
            'label' => $this->label,
            'type' => $this->type,
            'placeholder' => $this->placeholder,
            'settings' => $this->settings,
            'validation_rules' => $this->validation_rules,

            'form_section' => $this->whenLoaded('formSection', function () {
                return [
                    'id' => $this->formSection->id,
                    'title' => $this->formSection->title,
                ];
            }),
        ];
    }
}
