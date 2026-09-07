<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormSectionRequest;
use App\Http\Requests\UpdateFormSectionRequest;
use App\Http\Resources\FormsSectionResource;
use App\Models\FormSection;

class FormSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formsSections = FormSection::with('formVersion')->select()->paginate(5);
        return FormsSectionResource::collection($formsSections);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FormSectionRequest $request)
    {
        $formSection = Formsection::create($request->validated());
        return new FormsSectionResource($formSection);

    }

    /**
     * Display the specified resource.
     */
    public function show(FormSection $formSection)
    {
        return new FormsSectionResource($formSection);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFormSectionRequest $request, FormSection $formSection)
    {
        $formSection->update($request->validated());
        return new FormsSectionResource($formSection);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FormSection $formSection)
    {
        $formSection->delete();
        return response()->noContent();
    }
}
