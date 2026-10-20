<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApplicationAnswerRequest;
use App\Http\Resources\ApplicationAnswerResources;
use App\Models\Application;
use App\Models\ApplicationAnswer;
use App\Models\ApplicationInvitation;
use DB;
use Throwable;

use function Symfony\Component\Clock\now;

class ApplicationSubmisionController extends Controller
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
     *
     * @throws Throwable
     */
    public function store(CreateApplicationAnswerRequest $request, string $token)
    {

        $hashedToken = hash('sha256', $token);

        $applicationInvitation = ApplicationInvitation::where('token', $hashedToken)->where('expired_at', '>=', now())->firstOrFail()
            ->whereNull('used_at');

        DB::transaction(function () use ($request, $applicationInvitation) {
            $application = Application::create([
                'application_invitation_id' => $applicationInvitation->id,
                'job_hiring_id' => $applicationInvitation->jobHiring->id,
                'form_version_id' => $request->form_version_id,
                'status' => 'on_review',
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
            $applicationInvitation->used_at = now();
            $applicationInvitation->save();
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
