<?php

namespace App\Http\Controllers;

use App\Actions\FormFieldOption\ReorderFormFieldOption;
use App\Http\Requests\CreateFormFieldOptionRequest;
use App\Http\Requests\UpdateFormFieldOptionRequest;
use App\Http\Resources\FormFieldOptionResource;
use App\Models\FormField;
use App\Models\FormFieldOption;
use App\Models\FormVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FormFieldOptionController extends Controller
{
    public function index(FormField $formField)
    {
        $formFieldOptions = $formField->formFieldOptions()->select(
            'id',
            'form_field_id',
            'label',
            'value',
            'sort_order'
        )->orderBy('sort_order')->get();

        return FormFieldOptionResource::collection($formFieldOptions);
    }

    public function store(CreateFormFieldOptionRequest $request, FormField $formField)
    {
        $formFieldOption = DB::transaction(function () use ($request, $formField): FormFieldOption {
            $formVersion = FormVersion::query()->lockForUpdate()->findOrFail($formField->formSection->form_version_id);
            abort_unless($formVersion->status === 'draft', 409, 'Only draft forms can be edited.');
            abort_if($formVersion->applications()->exists(), 409, 'Forms with applications cannot be edited.');

            $formField = FormField::query()->findOrFail($formField->getKey());
            abort_unless(in_array($formField->type, ['select', 'radio', 'checkbox'], true), 409, 'This question type has no options.');

            return $formField->formFieldOptions()->create([
                ...$request->validated(),
                'sort_order' => ($formField->formFieldOptions()->max('sort_order') ?? 0) + 1,
            ]);
        });

        $formFieldOption->load('formField');

        return new FormFieldOptionResource($formFieldOption);
    }

    public function show(FormFieldOption $formFieldOption)
    {
        $formFieldOption = $formFieldOption->load('formField');

        return new FormFieldOptionResource($formFieldOption);
    }

    public function formFieldOptionsReorder(Request $request, FormFieldOption $formFieldOption, ReorderFormFieldOption $reorderFormFieldOption)
    {
        $validated = $request->validate(['sort_order' => ['required', 'integer', 'min:1']]);

        DB::transaction(function () use ($formFieldOption, $validated, $reorderFormFieldOption): void {
            $formVersion = FormVersion::query()->lockForUpdate()->findOrFail($formFieldOption->formField->formSection->form_version_id);
            abort_unless($formVersion->status === 'draft', 409, 'Only draft forms can be edited.');
            abort_if($formVersion->applications()->exists(), 409, 'Forms with applications cannot be edited.');

            $formFieldOption = FormFieldOption::query()->findOrFail($formFieldOption->getKey());
            if ($validated['sort_order'] > $formFieldOption->formField->formFieldOptions()->count()) {
                throw ValidationException::withMessages(['sort_order' => 'The position is outside this question.']);
            }

            $reorderFormFieldOption->execute($formFieldOption, $formFieldOption->sort_order, (int) $validated['sort_order']);
        });

        return response()->json([
            'message' => 'Form Field option moved successfully',
        ]);
    }

    public function update(UpdateFormFieldOptionRequest $request, FormFieldOption $formFieldOption)
    {
        DB::transaction(function () use ($request, $formFieldOption): void {
            $formVersion = FormVersion::query()->lockForUpdate()->findOrFail($formFieldOption->formField->formSection->form_version_id);
            abort_unless($formVersion->status === 'draft', 409, 'Only draft forms can be edited.');
            abort_if($formVersion->applications()->exists(), 409, 'Forms with applications cannot be edited.');

            FormFieldOption::query()->findOrFail($formFieldOption->getKey())->update($request->validated());
        });

        return response()->json([
            'message' => 'Form Field Option updated successfully',
        ]);
    }

    public function destroy(FormFieldOption $formFieldOption)
    {
        DB::transaction(function () use ($formFieldOption): void {
            $formVersion = FormVersion::query()->lockForUpdate()->findOrFail($formFieldOption->formField->formSection->form_version_id);
            abort_unless($formVersion->status === 'draft', 409, 'Only draft forms can be edited.');
            abort_if($formVersion->applications()->exists(), 409, 'Forms with applications cannot be edited.');

            FormFieldOption::query()->findOrFail($formFieldOption->getKey())->delete();
        });

        return response()->noContent();
    }
}
