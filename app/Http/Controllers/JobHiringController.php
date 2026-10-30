<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJobHiringRequest;
use App\Http\Requests\UpateJobHiringRequest;
use App\Http\Resources\JobHiringResource;
use App\Models\JobHiring;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class JobHiringController extends Controller
{
    public function publicIndex(Request $request): AnonymousResourceCollection
    {
        $jobHiring = JobHiring::query()->publiclyVisible()
            ->when($request->filled('department'), fn($query) => $query->where('department', $request->input('department')))
            ->paginate(10);


        return JobHiringResource::collection($jobHiring);
    }

    public function publicShow(string $jobHiring): JobHiringResource
    {
        return new JobHiringResource(JobHiring::query()->publiclyVisible()->findOrFail($jobHiring));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $jobHiring = JobHiring::query()
            ->when($request->filled('department'), fn($query) => $query->where('department', $request->input('department')))
            ->paginate(10);

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
        DB::transaction(function () use ($jobHiring): void {
            $jobHiring = JobHiring::query()->lockForUpdate()->findOrFail($jobHiring->getKey());

            abort_if($jobHiring->applications()->exists(), 409, 'Cannot delete a job hiring with applications.');

            $jobHiring->delete();
        });

        return response()->noContent();
    }

    // public function attachFormVersion(
    //     Request $request,
    //     JobHiring $jobHiring
    // ) {
    //     $data = $request->validate([
    //         'form_version_id' => [
    //             'required',
    //             'uuid',
    //             'exists:form_versions,id',
    //         ],
    //     ]);

    //     $jobHiring->formVersions()->syncWithoutDetaching(
    //         $data['form_version_id']
    //     );

    //     return response()->json([
    //         'message' => 'Form version attached successfully.',
    //     ]);
    // }
}
