<?php

namespace App\Actions\FormField;

use App\Models\FormField;
use Illuminate\Support\Facades\DB;
use Throwable;

class ReorderFormFields
{
    /**
     * @throws Throwable
     */
    public function execute(
        FormField $formField,
        int       $newOrder,
        string    $newSectionId,
    ): void
    {
        DB::transaction(function () use (
            $formField,
            $newOrder,
            $newSectionId
        ) {
            $oldSectionId = $formField->form_section_id;
            $oldOrder = $formField->sort_order;

            if ($oldSectionId === $newSectionId) {

                if ($newOrder < $oldOrder) {
                    FormField::where('form_section_id', $oldSectionId)
                        ->where('id', '!=', $formField->id)
                        ->where('sort_order', '>=', $newOrder)
                        ->where('sort_order', '<', $oldOrder)
                        ->increment('sort_order');
                }
                if ($newOrder > $oldOrder) {
                    FormField::where('form_section_id', $oldSectionId)
                        ->where('id', '!=', $formField->id)
                        ->where('sort_order', '>', $oldOrder)
                        ->where('sort_order', '<=', $newOrder)
                        ->decrement('sort_order');
                }
            } else {
                FormField::where('form_section_id', $oldSectionId)
                    ->where('sort_order', '>', $oldOrder)
                    ->decrement('sort_order');

                FormField::where('form_section_id', $newSectionId)
                    ->where('sort_order', '>=', $newOrder)
                    ->increment('sort_order');
            }
            $formField->update([
                'form_section_id' => $newSectionId,
                'sort_order' => $newOrder,
            ]);
        });
    }
}
