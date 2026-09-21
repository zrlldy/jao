<?php

namespace App\Http\Controllers;

use App\Models\ApplicationInvitation;
use App\Models\FormVersion;
use Illuminate\Http\Request;

class ApplicationForm extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ApplicationInvitation $application, string $token)
    {


        $applicationJob =  ApplicationInvitation::where('tokenHash', $token)->where('expired_at', '<=', now()->subDays(7))
            ->select('job_hiring_id')
            ->exists();


        $form = FormVersion::with('jobHirings', 'formTemplate', 'formSections.formFields.formFieldOptions')->where('status', 'published')->whereHas('jobHirings', function ($query) use ($applicationJob) {
            $query->where('job_hiring_id', $applicationJob);
        })->first();
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
