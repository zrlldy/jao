<?php

namespace App\Http\Controllers;

use App\Http\Resources\FormVersionResource;
use App\Models\ApplicationInvitation;
use App\Models\FormVersion;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Nette\Schema\Message;

class ApplicationForm extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $token)
    {
        try {
            $invitation = ApplicationInvitation::with('jobHiring:id', 'title')
                ->where('token', $token)
                ->where('expired_at', '>', now())
                ->select('job_hiring_id')
                ->firstOrFail();

            if ($invitation->expired_at->isPast()) {
                return response()->json([
                    'message' => 'This invitation has expired'
                ]);
            }



            $form = FormVersion::with(['formTemplate', 'formSections.formFields.formFieldOptions'])
                ->where('status', 'published')
                ->whereHas('jobHirings', function ($query) use ($invitation) {
                    $query->where('job_hiring_id', $invitation->job_hiring_id);
                })
                ->first();


            return (new FormVersionResource($form))->additional(['job_hiring' => $invitation->jobHiring?->title]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message" => 'Credentials Missmatch'
            ], 401);
        }
    }
}
