<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationInvitationRequest;
use App\Jobs\SendApplicantInvitation;
use App\Models\ApplicationInvitation;
use App\Models\JobHiring;
use App\Notifications\ApplicationInviteNotification;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ApplicationInvitationController extends Controller
{
    public function store(JobHiring $jobHiring, ApplicationInvitationRequest $request)
    {
        $data = $request->validated();
        $token =  Str::random(64);

        $invitation = $jobHiring->applicationInvitations()->create([
            ...$data,
            'token' => hash('sha256', $token),
            'expired_at' => Carbon::now()->addDays(7)
        ]);
        SendApplicantInvitation::dispatch($invitation, $token);
        return response()->json([
            'message' => 'Invitation sent to applicant'
        ]);
    }
}
