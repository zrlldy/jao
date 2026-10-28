<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJobFormVersionAssignment;
use App\Http\Resources\FormVersionResource;
use App\Http\Resources\JobFormVersionAssignmentResource;
use App\Models\FormVersion;
use App\Models\JobHiring;

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
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateJobFormVersionAssignment $request, JobHiring $jobHiring)
    {

        $ids = $request->validated('form_version_ids');

        $jobFormAssignment = $jobHiring->formVersions()->syncWithoutDetaching($ids
        );

        $versions = $jobHiring->formVersions()
            ->whereIn('form_versions.id', $ids)
            ->get();

        return new JobFormVersionAssignmentResource($versions);
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
