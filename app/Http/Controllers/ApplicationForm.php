<?php

namespace App\Http\Controllers;

use App\Http\Resources\FormVersionResource;
use App\Models\ApplicationInvitation;
use App\Models\FormVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicationForm extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ApplicationInvitation $application, string $token)
    {


        $applicationJob = ApplicationInvitation::where('token', $token)
            ->where('expired_at', '>', now())
            ->select('job_hiring_id')
            ->firstOrFail();

        // dd($applicationJob);


        $form = FormVersion::with([
            'jobHirings',
            'formTemplate',
            'formSections.formFields.formFieldOptions',
        ])
            ->where('status', 'published')
            ->whereHas('jobHirings', function ($query) use ($applicationJob) {
                $query->where('job_hiring_id', $applicationJob->job_hiring_id);
            })
            ->firstOrFail();

        return $form;

        // return new FormVersionResource($form);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
}
