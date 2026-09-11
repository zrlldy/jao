<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFormFieldRequest;
use App\Http\Requests\UpdateFormFieldRequest;
use App\Http\Resources\FormFieldResource;
use App\Models\FormField;
use App\Models\FormSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\form;

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
        $formField = FormField::with('formSection')
            ->select(
                'id',
                'form_section_id',
                'key',
                'label',
                'type',
                'placeholder',
                'settings',
                'validation_rules',
            )
            ->findOrFail($formField->id);
        return new FormFieldResource($formField);
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FormField $formField)
    {
        $formField->delete();
        return response()->noContent();
    }

    public function formFieldReorder(Request $request, FormField $formField)
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

        $oldSectionId = $formField->form_section_id;
        $newSectionId = $validated['form_section_id'];

        $oldOrder = $formField->sort_order;
        $newOrder = $validated['sort_order'];

        DB::transaction(function () use (
            $formField,
            $oldSectionId,
            $newSectionId,
            $oldOrder,
            $newOrder
        ) {

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

        return response()->json([
            'message' => 'Form Field Reordered Successfully',
        ]);
    }
}
