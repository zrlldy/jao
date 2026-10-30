<?php

namespace App\Http\Controllers;

use App\Actions\FormField\ReorderFormFields;
use App\Http\Requests\CreateFormFieldRequest;
use App\Http\Requests\UpdateFormFieldRequest;
use App\Http\Resources\FormFieldResource;
use App\Models\ApplicationAnswer;
use App\Models\FormField;
use App\Models\FormSection;
use App\Models\FormVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FormFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FormSection $formSection)
    {
        $formField = $formSection->formFields()->select()->orderBy('sort_order')->get();

        return FormFieldResource::collection($formField);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateFormFieldRequest $request, FormSection $formSection)
    {
        $formField = DB::transaction(function () use ($request, $formSection): FormField {
            $formVersion = FormVersion::query()->lockForUpdate()->findOrFail($formSection->form_version_id);
            abort_unless($formVersion->status === 'draft', 409, 'Only draft forms can be edited.');
            abort_if($formVersion->applications()->exists(), 409, 'Forms with applications cannot be edited.');

            $formSection = FormSection::query()->findOrFail($formSection->getKey());

            return $formSection->formFields()->create([
                ...$request->validated(),
                'sort_order' => ($formSection->formFields()->max('sort_order') ?? 0) + 1,
            ]);
        });
        $formField->load('formSection');

        return new FormFieldResource($formField);
    }

    /**
     * Display the specified resource.
     */
    public function show(FormField $formField)
    {
        $formField = $formField->load('formSection');

        return new FormFieldResource($formField);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FormField $formField)
    {
        DB::transaction(function () use ($formField): void {
            $formVersion = FormVersion::query()->lockForUpdate()->findOrFail($formField->formSection->form_version_id);
            abort_unless($formVersion->status === 'draft', 409, 'Only draft forms can be edited.');
            abort_if($formVersion->applications()->exists(), 409, 'Forms with applications cannot be edited.');

            $formField = FormField::query()->findOrFail($formField->getKey());
            abort_if(ApplicationAnswer::query()->where('form_field_id', $formField->id)->exists(),
                409, 'Cannot delete a question with saved answers.');

            $formField->delete();
        });

        return response()->noContent();
    }

    public function formFieldReorder(Request $request, FormField $formField, ReorderFormFields $reorderFormFields)
    {
        $validated = $request->validate([
            'form_section_id' => [
                'required',
                'uuid',
                'exists:form_sections,id',
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        DB::transaction(function () use ($formField, $validated, $reorderFormFields): void {
            $formVersion = FormVersion::query()->lockForUpdate()->findOrFail($formField->formSection->form_version_id);
            abort_unless($formVersion->status === 'draft', 409, 'Only draft forms can be edited.');
            abort_if($formVersion->applications()->exists(), 409, 'Forms with applications cannot be edited.');

            $formField = FormField::query()->findOrFail($formField->getKey());
            $destination = FormSection::query()->findOrFail($validated['form_section_id']);
            if ($destination->form_version_id !== $formVersion->id) {
                throw ValidationException::withMessages(['form_section_id' => 'The section must belong to this form version.']);
            }

            $lastPosition = $destination->formFields()->count()
                + ($destination->id === $formField->form_section_id ? 0 : 1);
            if ($validated['sort_order'] > $lastPosition) {
                throw ValidationException::withMessages(['sort_order' => 'The position is outside this section.']);
            }

            $reorderFormFields->execute(
                formField: $formField,
                newOrder: (int) $validated['sort_order'],
                newSectionId: $destination->id,
            );
        });

        return response()->json([
            'message' => 'Form Field Reordered Successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFormFieldRequest $request, FormField $formField)
    {
        DB::transaction(function () use ($request, $formField): void {
            $formVersion = FormVersion::query()->lockForUpdate()->findOrFail($formField->formSection->form_version_id);
            abort_unless($formVersion->status === 'draft', 409, 'Only draft forms can be edited.');
            abort_if($formVersion->applications()->exists(), 409, 'Forms with applications cannot be edited.');

            FormField::query()->findOrFail($formField->getKey())->update($request->validated());
        });

        return response()->json(
            ['message' => 'Form Field Updated']
        );
    }
}
