<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormSectionRequest;
use App\Http\Requests\UpdateFormSectionRequest;
use App\Http\Resources\FormsSectionResource;
use App\Models\FormSection;
use App\Models\FormVersion;

class FormSectionController extends Controller
{
    public function index(FormVersion $formVersion)
    {
        $formSections = $formVersion
            ->formSections()
            ->orderBy('sort_order')
            ->paginate(5);

        return FormsSectionResource::collection($formSections);
    }

    public function store(
        FormSectionRequest $request,
        FormVersion $formVersion
    ) {
        $formSection = $formVersion
            ->formSections()
            ->create($request->validated());

        return new FormsSectionResource($formSection);
    }

    public function show(FormSection $formSection)
    {
        return new FormsSectionResource($formSection);
    }

    public function update(
        UpdateFormSectionRequest $request,
        FormSection $formSection
    ) {
        $formSection->update($request->validated());

        return new FormsSectionResource($formSection);
    }

    public function destroy(FormSection $formSection)
    {
        $formSection->delete();

        return response()->noContent();
    }
}
