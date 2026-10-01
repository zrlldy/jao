<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApplicationAnswerRequest;
use App\Http\Resources\ApplicationAnswerResources;
use App\Http\Resources\FormVersionResource;
use App\Models\Application;
use App\Models\ApplicationAnswer;
use App\Models\ApplicationInvitation;
use App\Models\FormVersion;
use DB;
use Illuminate\Http\Request;
use Throwable;

class ApplicationFormAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $applicationAnswer = ApplicationAnswer::with(['application', 'formField'])->paginate(10);

        return ApplicationAnswerResources::collection($applicationAnswer);
    }

    /**
     * Store a newly created resource in storage.a
     * @throws Throwable
     */
    public function store(CreateApplicationAnswerRequest $request, string $token)
    {

        $applicationInvitation = ApplicationInvitation::where('token', $token)->where('expired_at', '>=', now()->subDays(7))->firstOrFail();

        DB::transaction(function () use ($request, $applicationInvitation) {
            $application = Application::create([
                'application_invitation_id' => $applicationInvitation->id,
                'job_id' => $applicationInvitation->jobHiring->job_id,
                'form_version_id' => $request->form_version_id,
                'submitted_at' => now(),
            ]);

            $answers = [];

            foreach ($request->answers as $answer) {
                $answers[] = [
                    'application_id' => $application->id,
                    'form_field_id' => $answer['form_field_id'],
                    'value' => $answer['value'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            ApplicationAnswer::insert($answers);
        });

        return response()->json(['message' => 'Application submitted successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(ApplicationAnswer $applicationAnswer)
    {

        $applicationAnswer->load('formField', 'application');
        return new ApplicationAnswerResources($applicationAnswer);
    }
}
