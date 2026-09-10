<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJobHiringRequest;
use App\Http\Requests\UpateJobHiringRequest;
use App\Http\Resources\JobHiringResource;
use App\Models\JobHiring;
use Illuminate\Http\Request;

class JobHiringController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobHiring = JobHiring::with('department:id,name', 'employmentType:id,name', 'location:id,name')->select('id', 'department_id', 'employment_type_id', 'location_id', 'title', 'slug', 'description', 'requirements', 'status', 'published_at', 'closed_at')->paginate(10);

        return JobHiringResource::collection($jobHiring);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateJobHiringRequest $request)
    {

        $jobHiring = JobHiring::create($request->validated());

        return new JobHiringResource($jobHiring);
    }

    /**
     * Display the specified resource.
     */
    public function show(JobHiring $jobHiring)
    {
        $jobHiring->load('department:id,name', 'employmentType:id,name', 'location:id,name')->select('id', 'department_id', 'employment_type_id', 'location_id', 'title', 'slug', 'description', 'requirements', 'status', 'published_at', 'closed_at');

        return new JobHiringResource($jobHiring);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpateJobHiringRequest $request, JobHiring $jobHiring)
    {
        $jobHiring->update($request->validated());
        return new JobHiringResource($jobHiring);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobHiring $jobHiring)
    {
        $jobHiring->delete();
        return response()->noContent();
    }

    public function attachFormVersion(
        Request $request,
        JobHiring $jobHiring
    ) {
        $data = $request->validate([
            'form_version_id' => [
                'required',
                'uuid',
                'exists:form_versions,id',
            ],
        ]);

        $jobHiring->formVersions()->attach(
            $data['form_version_id']
        );

        return response()->json([
            'message' => 'Form version attached successfully.',
        ]);
    }
}
