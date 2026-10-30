<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFormVersionRequest;
use App\Http\Requests\UpdateFormVersionRequest;
use App\Http\Resources\FormVersionResource;
use App\Models\ApplicationAnswer;
use App\Models\ApplicationInvitation;
use App\Models\FormTemplate;
use App\Models\FormVersion;
use Illuminate\Support\Facades\DB;

class FormVersionController extends Controller
{
    public function index(FormTemplate $formTemplate)
    {
        $formVersions = $formTemplate
            ->formVersions()
            ->select(
                'id',
                'form_template_id',
                'published_at',
                'version',
                'status'
            )
            ->orderByDesc('version')
            ->paginate(10);

        return FormVersionResource::collection($formVersions);
    }

    public function store(
        CreateFormVersionRequest $request,
        FormTemplate $formTemplate
    ) {

        $formVersion = DB::transaction(function () use ($request, $formTemplate) {
            $lockedTemplate = FormTemplate::query()
                ->whereKey($formTemplate->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $latestVersion = $lockedTemplate->formVersions()
                ->orderByDesc('version')
                ->lockForUpdate()
                ->value('version');

            return $lockedTemplate->formVersions()->create([
                ...$request->validated(),
                'version' => ((int) ($latestVersion ?? 0)) + 1,
                'published_at' => null,
            ]);
        }, 3);

        return new FormVersionResource($formVersion);
    }

    public function show(FormVersion $formVersion)
    {
        return new FormVersionResource($formVersion);
    }

    public function update(
        UpdateFormVersionRequest $request,
        FormVersion $formVersion
    ) {
        $formVersion = DB::transaction(function () use ($request, $formVersion): FormVersion {
            $formVersion = FormVersion::query()->lockForUpdate()->findOrFail($formVersion->getKey());
            $data = $request->validated();

            if (isset($data['status']) && $data['status'] !== $formVersion->status) {
                abort_if($formVersion->status !== 'draft' && $data['status'] !== 'archived', 409, 'A published or archived form cannot be reopened.');

                if ($data['status'] === 'published') {
                    abort_unless($formVersion->formSections()->whereHas('formFields')->exists(), 409, 'A form needs a question before publication.');
                    abort_if($formVersion->formSections()->whereHas('formFields', fn ($query) => $query
                        ->whereIn('type', ['select', 'radio', 'checkbox'])
                        ->doesntHave('formFieldOptions'))->exists(), 409, 'Choice questions need options before publication.');
                    $data['published_at'] = today();
                }
            }

            $formVersion->update($data);

            return $formVersion;
        });

        return new FormVersionResource($formVersion);
    }

    public function destroy(FormVersion $formVersion)
    {
        DB::transaction(function () use ($formVersion): void {
            $formVersion = FormVersion::query()->lockForUpdate()->findOrFail($formVersion->getKey());

            abort_if($formVersion->applications()->exists(), 409, 'Cannot delete a form version with applications.');
            abort_if(ApplicationAnswer::query()->whereHas('formField.formSection', fn ($query) => $query
                ->where('form_version_id', $formVersion->id))->exists(), 409, 'Cannot delete a form version with saved answers.');
            abort_if($formVersion->jobHirings()->exists()
                || ApplicationInvitation::query()->where('form_version_id', $formVersion->id)->exists(),
                409, 'Cannot delete an assigned or invited form version.');

            $formVersion->delete();
        });

        return response()->noContent();
    }
}
