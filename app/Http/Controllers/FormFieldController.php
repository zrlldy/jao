<?php

namespace App\Http\Controllers;

use App\Actions\FormField\ReorderFormFields;
use App\Http\Requests\CreateFormFieldRequest;
use App\Http\Requests\UpdateFormFieldRequest;
use App\Http\Resources\FormFieldResource;
use App\Models\FormField;
use App\Models\FormSection;
use Illuminate\Http\Request;

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

        $formsection = $formSection->formFields()->create([
            ...$request->validated(),
            'sort_order' => ($formSection->formFields->max('sort_order') ?? 0) + 1,

        ]);

        return new FormFieldResource($formSection);
    }


    /**
     * Display the specified resource.
     */
    public function show(FormField $formField)
    {


        $formField = $formField->load('formSection')->select(
            'id',
            'form_section_id',
            'key',
            'label',
            'type',
            'placeholder',
            'settings',
            'validation_rules',
        );

        return new FormFieldResource($formField);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FormField $formField)
    {
        $formField->delete();
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

        $reorderFormFields->execute(
            formField: $formField,
            newOrder: $validated['sort_order'],
            newSectionId: $validated['form_section_id'],
        );


        return response()->json([
            'message' => 'Form Field Reordered Successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFormFieldRequest $request, FormField $formField)
    {
        $formField->update($request->validated());
        return response()->json(
            ['message' => 'Form Field Updated']
        );
    }
}
