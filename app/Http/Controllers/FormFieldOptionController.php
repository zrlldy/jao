<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFormFieldOptionRequest;
use App\Http\Requests\UpdateFormFieldOptionRequest;
use App\Http\Resources\FormFieldOptionResource;
use App\Models\FormField;
use App\Models\FormFieldOption;

class FormFieldOptionController extends Controller
{
    public function index(FormField $formField)
    {
        $formFieldOptions = $formField->formFieldOptions->select(
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
        $formField->formFieldOptions()->create([...$request->validated(),
            'sort_order' => ($formField->formFieldOptions()->max('sort_order') ?? 0) + 1
        ]);
        return new FormFieldOptionResource($formField->formFieldOptions);
    }

    public function show(FormFieldOption $formFieldOption)
    {
        $formFieldOption = $formFieldOption->load('formField:id,name,type');
        return new FormFieldOptionResource($formFieldOption);
    }


    public function update(UpdateFormFieldOptionRequest $request, FormFieldOption $formFieldOption)
    {
        $formFieldOption->update($request->validated());
        return response()->json([
            'message' => 'Form Field Option updated successfully'
        ]);
    }

    public function formFieldOptionsReorder()
    {

    }

    public function destroy(FormFieldOption $formFieldOption)
    {
        $formFieldOption->delete();
        return response()->noContent();
    }
}
