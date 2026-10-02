<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApplicationAnswerRequest;
use App\Http\Resources\ApplicationAnswerResources;
use App\Models\ApplicationAnswer;

class ApplicationAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ApplicationAnswer $applicationAnswer)
    {
        $applicationAnswer->load('application', 'formField')->select('id', 'value');

        return ApplicationAnswerResources::collection($applicationAnswer);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateApplicationAnswerRequest $request)
    {


        return response()->json(['message' => 'Application submitted successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(ApplicationAnswer $applicationAnswer) {}
}
