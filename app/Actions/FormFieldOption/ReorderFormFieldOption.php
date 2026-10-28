<?php

namespace App\Actions\FormFieldOption;

use App\Models\FormFieldOption;
use Illuminate\Support\Facades\DB;
use Throwable;

class ReorderFormFieldOption
{

    /**
     * @throws Throwable
     */
    public function execute(
        FormFieldOption $formFieldOption,
        int             $oldSortOrder,
        int             $newSortOrder
    ): void
    {
        DB::transaction(function () use ($formFieldOption, $oldSortOrder, $newSortOrder) {

            if ($oldSortOrder > $newSortOrder) {
                FormFieldOption::where('form_field_id', $formFieldOption)
                    ->where('sort_order', '>=', $newSortOrder)
                    ->where('sort_order', '<', $oldSortOrder)
                    ->increment('sort_order');
            } elseif ($oldSortOrder < $newSortOrder) {
                FormFieldOption::where('form_field_id', $formFieldOption)
                    ->where('sort_order', '>', $oldSortOrder)
                    ->where('sort_order', '<=', $newSortOrder)
                    ->decrement('sort_order');
            }
            $formFieldOption->update(['sort_order' => $newSortOrder]);
        });

    }

}
