<?php

namespace App\Http\Controllers;

use App\Http\Resources\FormVersionResource;
use App\Models\ApplicationInvitation;
use App\Models\FormVersion;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicationForm extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $token)
    {
        try {
            $applicationJob = ApplicationInvitation::where('token', $token)
                ->where('expired_at', '>', now())
                ->select('job_hiring_id')
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message" => 'Credentials Missmatch'
            ], 401);
        }
        $form = FormVersion::with(['jobHirings', 'formTemplate', 'formSections.formFields.formFieldOptions'])
            ->where('status', 'published')
            ->whereHas('jobHirings', function ($query) use ($applicationJob) {
                $query->where('job_hiring_id', $applicationJob->job_hiring_id);
            })
            ->first();
        return (new FormVersionResource($form))->additional(['job_hiring' => $applicationJob->jobHiring?->title]);
    }
}
