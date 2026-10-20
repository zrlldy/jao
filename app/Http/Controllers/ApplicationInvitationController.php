<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationInvitationRequest;
use App\Jobs\SendApplicantInvitation;
use App\Models\ApplicationInvitation;
use App\Models\JobHiring;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ApplicationInvitationController extends Controller
{
    public function store(JobHiring $jobHiring, ApplicationInvitationRequest $request)
    {
        $data = $request->validated();
        $token = Str::random(64);

        $invitation = $jobHiring->applicationInvitations()->create([
            ...$data,
            'token' => hash('sha256', $token),
            'expired_at' => Carbon::now()->addDays(7),
        ]);

        SendApplicantInvitation::dispatch($invitation, $token);

        return response()->json([
            'message' => 'Invitation sent to applicant',
        ]);
    }

    public function checkToken(string $token, ApplicationInvitation $ApplicationInvite)
    {

        $hashedToken = hash('sha256', $token);
        $tokenCheck = $ApplicationInvite->where('token', $hashedToken)->exists();

        if (! $tokenCheck) {
            return response()->json([
                'message' => 'Invalid invitation token.',
            ], 401);
        }

        return response()->json([
            'message' => 'Invitation token verified successfully.',
        ]);
    }
}
