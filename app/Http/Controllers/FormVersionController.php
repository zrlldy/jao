<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFormVersionRequest;
use App\Http\Requests\UpdateFormVersionRequest;
use App\Http\Resources\FormVersionResource;
use App\Models\FormTemplate;
use App\Models\FormVersion;

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
    )
    {
        $nextVersion =
            ($formTemplate->formVersions()->max('version') ?? 0) + 1;

        $formVersion = $formTemplate->formVersions()->create([
            ...$request->validated(),
            'version' => $nextVersion,
            'published_at' => today(),
        ]);

        return new FormVersionResource($formVersion);
    }

    public function show(FormVersion $formVersion)
    {
        return new FormVersionResource($formVersion);
    }

    public function update(
        UpdateFormVersionRequest $request,
        FormVersion              $formVersion
    )
    {
        $formVersion->update($request->validated());
        return new FormVersionResource($formVersion);
    }

    public function destroy(FormVersion $formVersion)
    {
        $formVersion->delete();
        return response()->noContent();
    }
}
