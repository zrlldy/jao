<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatejobFormVersionAssignment;
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
    public function store(CreatejobFormVersionAssignment $request, JobHiring $jobHiring)
    {

        $data = $request->validated();

        $jobFormAssignment = $jobHiring->formVersions()->syncWithoutDetaching($data['form_version_ids']);

        return new JobFormVersionAssignmentResource($jobFormAssignment);
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
