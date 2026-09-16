<?php

namespace App\Http\Controllers;

use App\Http\Resources\FormVersionResource;
use App\Http\Resources\JobFormVersionAssignmentResource;
use App\Http\Resources\JobHiringResource;
use App\Models\FormVersion;
use App\Models\JobHiring;
use Illuminate\Http\Request;

class JobFormVersionAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(JobHiring $jobHiring)
    {

        $formVersions = $jobHiring->formVersions()->with('formTemplate')->paginate(10);
        return FormVersionResource::collection($formVersions)
            ->additional([
                'job_hiring' => [
                    'id' => $jobHiring->id,
                    'title' => $jobHiring->title,
                ],
            ])
        ;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, JobHiring $jobHiring)
    {

        $data = $request->validate([
            'form_version_ids' => ['required', 'exists:form_version_id', 'uuid'],
            'form_version_ids.*' => ['sometimes,exists:form_version_id', 'uuid']
        ]);

        $jobHiring->formVersions()->syncWithoutDetaching($data['form_version_ids']);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobHiring $jobHiring, FormVersion $formVersion)
    {
        $jobHiring->formVersions()->detach($formVersion->id);
        return response()->noContent();
    }
}
