<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationInvitationRequest;
use App\Models\ApplicationInvitation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use function Symfony\Component\Clock\now;

class ApplicationInvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ApplicationInvitationRequest $request)
    {
        $data = $request->validated();
        $token =  Str::random(64);

        $invitation = ApplicationInvitation::create([
            'job_hiring_id' => $data['job_hiring_id'],
            'email' => $data['email'],
            'token' => hash('sha256', $token),
            'expired_at' => Carbon::now()->addDays(7),
        ]);
        return response()->json([
            'message' => 'Invitation sent to applicant'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ApplicationInvitation $applicationInvitation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApplicationInvitation $applicationInvitation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApplicationInvitation $applicationInvitation)
    {
        //
    }
}
