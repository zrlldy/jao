<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApplicationResource;
use App\Models\Application;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $application = Application::with('jobHiring')->select(['id', 'applicant_no', 'status', 'submitted_at', 'reviewed_by', 'rejection_reason', 'reviewed_at', 'deleted_at'])->paginate(10);

        return ApplicationResource::collection($application);
    }

    public function show(Application $application)
    {
        return new ApplicationResource($application->load('jobHiring'));
    }
}
