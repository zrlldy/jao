<?php

namespace App\Http\Controllers;

use App\Http\Resources\FormVersionResource;
use App\Models\ApplicationInvitation;
use App\Models\FormVersion;
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
                ->where('expired_at', '>=', now())
                ->select('job_hiring_id')
                ->firstOrFail();

            $form = FormVersion::with(['formTemplate', 'formSections.formFields.formFieldOptions'])
                ->where('status', 'published')
                ->whereHas('jobHirings', function ($query) use ($invitation) {
                    $query->where('job_hiring_id', $invitation->job_hiring_id);
                })
                ->first();

            return (new FormVersionResource($form))->additional(['job_hiring' => $invitation->jobHiring?->title]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Credentials Missmatch',
            ], 401);
        }
    }
}
