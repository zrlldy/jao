<?php

namespace App\Http\Controllers;

use App\Http\Resources\FormVersionResource;
use App\Models\ApplicationInvitation;
use App\Models\FormVersion;
use Illuminate\Http\Request;

class ApplicationFormAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ApplicationInvitation $applicationInvitation, string $token)
    {



        ApplicationInvitation::where('tokenHash', $token)->where('expired_at', '<=', now()->subDays(7))->exists();


        $jobHiring = $applicationInvitation->jobHiring?->id;

        $formVersion = FormVersion::with(['jobHirings', 'formTemplate', 'formSections.formFields.formFieldOptions'])->where('status', 'published')->whereHas('jobHirings', function ($query)  use ($jobHiring) {
            $query->where('id', $jobHiring);
        });


        return new FormVersionResource($formVersion);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
