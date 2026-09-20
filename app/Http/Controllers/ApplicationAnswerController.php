<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApplicationAnswerResources;
use App\Http\Resources\FormFieldResource;
use App\Models\ApplicationAnswer;
use Illuminate\Http\Request;

class ApplicationAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ApplicationAnswer $applicationAnswer,)
    {

        $applicationAnswer->load('application', 'formField')->select('id', 'value');

        return ApplicationAnswerResources::collection($applicationAnswer);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ApplicationAnswerResources $request)
    {


        // return new FormFieldResource();
    }

    /**
     * Display the specified resource.
     */
    public function show(ApplicationAnswer $applicationAnswer) {}
}
