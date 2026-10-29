<?php

namespace App\Http\Controllers;

use App\Http\Resources\FormVersionResource;
use App\Models\ApplicationInvitation;
use App\Models\FormVersion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ApplicationFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $token)
    {
        try {

            $hashedToken = hash('sha256', $token);
            $invitation = ApplicationInvitation::with(['jobHiring:id,title'])
                ->where('token', $hashedToken)
                ->where('expired_at', '>=', today()->toDateString())
                ->whereNull('used_at')
                ->whereHas('jobHiring', fn (Builder $query) => $query->publiclyVisible())
                ->select('job_hiring_id', 'form_version_id', 'email')
                ->firstOrFail();

            $form = FormVersion::with(['formTemplate', 'formSections.formFields.formFieldOptions'])
                ->whereKey($invitation->form_version_id)
                ->where('status', 'published')
                ->whereHas('jobHirings', function ($query) use ($invitation) {
                    $query->where('job_hiring_id', $invitation->job_hiring_id);
                })
                ->firstOrFail();

            return (new FormVersionResource($form))->additional(['job_hiring' => $invitation->jobHiring?->title, 'email' => $invitation->email]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Credentials Missmatch',
            ], 401);
        }
    }
}
