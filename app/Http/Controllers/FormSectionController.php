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
        FormVersion        $formVersion
    )
    {
        $formSection = $formVersion
            ->formSections()
            ->create([...$request->validated(),
                'sort_order' => ($formVersion->formSections()->max('sort_order') ?? 0) + 1
            ]);
        return new FormsSectionResource($formSection);
    }

    public function show(FormSection $formSection)
    {
        return new FormsSectionResource($formSection);
    }

    public function sectionReorder(FormSection $formSection)
    {
        request()->validate([
            'sort_order' => 'required|integer'
        ]);

        $formSection = FormSection::find(request('form_section_id'));
        $oldSortOrder = $formSection->sort_order;
        $newSortOrder = request('sort_order');
        $formVersionId = $formSection->form_version_id;
        
        if ($oldSortOrder !== $newSortOrder) {
            if ($oldSortOrder > $newSortOrder) {
                FormSection::where('form_version_id', $formVersionId)
                    ->where('sort_order', '<', $oldSortOrder)
                    ->where('sort_order', '>=', $newSortOrder)
                    ->increment('sort_order');
            } else {
                FormSection::where('form_version_id', $formVersionId)
                    ->where('sort_order', '>', $oldSortOrder)
                    ->where('sort_order', '<=', $newSortOrder)
                    ->decrement('sort_order');
            }
            $formSection->update(['sort_order' => $newSortOrder]);
        }
        return response()->json([
            'message' => 'Form section reordered successfully.'
        ]);
    }

    public function update(
        UpdateFormSectionRequest $request,
        FormSection              $formSection
    )
    {
        $formSection->update($request->validated());
        return new FormsSectionResource($formSection);
    }

    public function destroy(FormSection $formSection)
    {
        $formSection->delete();
        return response()->noContent();
    }
}
