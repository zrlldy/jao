<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFormVersionRequest;
use App\Http\Requests\UpdateFormVersionRequest;
use App\Http\Resources\FormVersionResource;
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
        FormTemplate             $formTemplate
    ) {

        DB::transaction(function () use ($request, $formTemplate) {
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
                'published_at' => today(),
            ]);
        }, 3);
    }

    public function show(FormVersion $formVersion)
    {
        return new FormVersionResource($formVersion);
    }

    public function update(
        UpdateFormVersionRequest $request,
        FormVersion              $formVersion
    ) {
        $formVersion->update($request->validated());
        return new FormVersionResource($formVersion);
    }

    public function destroy(FormVersion $formVersion)
    {
        $formVersion->delete();
        return response()->noContent();
    }
}
