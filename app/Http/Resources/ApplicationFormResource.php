<?php

namespace App\Http\Resources;

use App\Models\FormSection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationFormResource extends JsonResource
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
            'form_template' => $this->whenLoaded('formTemplate', function () {
                return [
                    'name' => $this->formTemplate->name,
                    'description' => $this->formTemplate->description,

                ];
            }),
            'form_version' => $this->whenLoaded('formVersion', function () {
                return [
                    'version' => $this->formVersion->name,
                    'form_section' => $this->whenLoaded('formSection', function () {
                        return [
                            'section_title' => $this->formSection->title,
                            'section_description' => $this->formSection->description,
                            'section_order' => $this->formSection->sort_order,
                            'form_field' => $this->whenLoaded('formField', function () {
                                return    [
                                    'form_field_key' => $this->formField->key,
                                    'form_field_label' => $this->formField->label,
                                    'form_field_type' => $this->formField->type,
                                    'form_field_placeholder' => $this->formField->placeholder,
                                    'form_field_default_value' => $this->formField->default_value,
                                    'form_field_is_required' => $this->formField->is_required,
                                    'form_field_sort_order' => $this->formField->sort_order,
                                    'form_field_settings' => $this->formField->settings,
                                    'form_field_validation_rules' => $this->formField->validation_rules,
                                    'form_field_options' => $this->whenLoaded('formFieldOption', function () {
                                        return [
                                            'form_field_option_label' => $this->formFieldOption->label,
                                            'form_field_option_value' => $this->formFieldOption->value,
                                            'form_field_option_sort_order' => $this->formFieldOption->sort_order,
                                        ];
                                    })
                                ];
                            })
                        ];
                    })
                ];
            }),
        ];
    }
}
