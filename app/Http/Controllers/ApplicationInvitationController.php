<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationInvitationRequest;
use App\Jobs\SendApplicantInvitation;
use App\Models\JobHiring;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ApplicationInvitationController extends Controller
{
    public function store(JobHiring $jobHiring, ApplicationInvitationRequest $request): JsonResponse
    {
        $data = $request->validated();

        abort_unless(
            JobHiring::query()->publiclyVisible()->whereKey($jobHiring->getKey())->exists(),
            422,
            'The job must be publicly available and have an assigned published form before sending invitations.',
        );

        $formVersion = $jobHiring->formVersions()
            ->where('form_versions.status', 'published')
            ->orderByDesc('form_versions.version')
            ->first();

        abort_unless(
            $formVersion,
            422,
            'The job must be publicly available and have an assigned published form before sending invitations.',
        );

        $token = Str::random(64);

        $invitation = $jobHiring->applicationInvitations()->create([
            ...$data,
            'form_version_id' => $formVersion->id,
            'token' => hash('sha256', $token),
            'expired_at' => today()->addDays(7)->toDateString(),
        ]);

        SendApplicantInvitation::dispatch($invitation, $token);

        return response()->json([
            'message' => 'Invitation queued for delivery',
        ]);
    }
}
