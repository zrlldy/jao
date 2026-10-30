<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormSectionRequest;
use App\Http\Requests\UpdateFormSectionRequest;
use App\Http\Resources\FormsSectionResource;
use App\Models\ApplicationAnswer;
use App\Models\FormSection;
use App\Models\FormVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FormSectionController extends Controller
{
    public function index(FormVersion $formVersion)
    {
        $formSections = $formVersion
            ->formSections()
            ->orderBy('sort_order')
            ->paginate(10);

        return FormsSectionResource::collection($formSections);
    }

    public function store(
        FormSectionRequest $request,
        FormVersion $formVersion
    ) {
        $formSection = DB::transaction(function () use ($request, $formVersion): FormSection {
            $formVersion = FormVersion::query()->lockForUpdate()->findOrFail($formVersion->getKey());
            abort_unless($formVersion->status === 'draft', 409, 'Only draft forms can be edited.');
            abort_if($formVersion->applications()->exists(), 409, 'Forms with applications cannot be edited.');

            return $formVersion->formSections()->create([
                ...$request->validated(),
                'sort_order' => ($formVersion->formSections()->max('sort_order') ?? 0) + 1,
            ]);
        });

        return new FormsSectionResource($formSection);
    }

    public function show(FormSection $formSection)
    {
        return new FormsSectionResource($formSection);
    }

    public function sectionReorder(FormSection $formSection)
    {
        request()->validate(['sort_order' => ['required', 'integer', 'min:1']]);
        $newSortOrder = request()->integer('sort_order');

        DB::transaction(function () use ($formSection, $newSortOrder): void {
            $formVersion = FormVersion::query()->lockForUpdate()->findOrFail($formSection->form_version_id);
            abort_unless($formVersion->status === 'draft', 409, 'Only draft forms can be edited.');
            abort_if($formVersion->applications()->exists(), 409, 'Forms with applications cannot be edited.');

            $formSection = FormSection::query()->lockForUpdate()->findOrFail($formSection->getKey());
            if ($newSortOrder > $formVersion->formSections()->count()) {
                throw ValidationException::withMessages(['sort_order' => 'The position is outside this form.']);
            }

            $oldSortOrder = $formSection->sort_order;
            if ($oldSortOrder > $newSortOrder) {
                $formVersion->formSections()
                    ->where('sort_order', '<', $oldSortOrder)
                    ->where('sort_order', '>=', $newSortOrder)
                    ->increment('sort_order');
            } elseif ($oldSortOrder < $newSortOrder) {
                $formVersion->formSections()
                    ->where('sort_order', '>', $oldSortOrder)
                    ->where('sort_order', '<=', $newSortOrder)
                    ->decrement('sort_order');
            }

            $formSection->update(['sort_order' => $newSortOrder]);
        });

        return response()->json([
            'message' => 'Form section reordered successfully.',
        ]);
    }

    public function update(
        UpdateFormSectionRequest $request,
        FormSection $formSection
    ) {
        $formSection = DB::transaction(function () use ($request, $formSection): FormSection {
            $formVersion = FormVersion::query()->lockForUpdate()->findOrFail($formSection->form_version_id);
            abort_unless($formVersion->status === 'draft', 409, 'Only draft forms can be edited.');
            abort_if($formVersion->applications()->exists(), 409, 'Forms with applications cannot be edited.');

            $formSection = FormSection::query()->findOrFail($formSection->getKey());
            $formSection->update($request->validated());

            return $formSection;
        });

        return new FormsSectionResource($formSection);
    }

    public function destroy(FormSection $formSection)
    {
        DB::transaction(function () use ($formSection): void {
            $formVersion = FormVersion::query()->lockForUpdate()->findOrFail($formSection->form_version_id);
            abort_unless($formVersion->status === 'draft', 409, 'Only draft forms can be edited.');
            abort_if($formVersion->applications()->exists(), 409, 'Forms with applications cannot be edited.');

            $formSection = FormSection::query()->findOrFail($formSection->getKey());
            abort_if(ApplicationAnswer::query()
                ->whereIn('form_field_id', $formSection->formFields()->select('id'))
                ->exists(), 409, 'Cannot delete a section with saved answers.');

            $formSection->delete();
        });

        return response()->noContent();
    }
}
