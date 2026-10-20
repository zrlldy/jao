<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApplicationAnswerResources;
use App\Models\Application;
use App\Models\ApplicationAnswer;

class ApplicationAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Application $application)
    {
        $applicationAnswers = $application
            ->applicationAnswers()
            ->with(['application', 'formField'])
            ->select([
                'id',
                'application_id',
                'form_field_id',
                'value',
            ])
            ->paginate(10);

        return ApplicationAnswerResources::collection($applicationAnswers);
    }

    /**
     * Display the specified resource.
     */
    public function show(ApplicationAnswer $applicationAnswer)
    {
        return new ApplicationAnswerResources($applicationAnswer->load('application', 'formField'));
    }
}
