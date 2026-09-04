<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFormTemplateRequest;
use App\Http\Requests\UpdateFormTemplateRequest;
use App\Http\Resources\FormTemplateResource;
use App\Models\FormTemplate;
use Illuminate\Http\Request;

class FormTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formTemplate = FormTemplate::select('id', 'name', 'description', 'is_active',)->orderBy('name', 'asc')->paginate(10);
        return FormTemplateResource::collection($formTemplate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateFormTemplateRequest $request)
    {

        $formTemplate = FormTemplate::create($request->validated());
        return new FormTemplateResource($formTemplate);
    }

    /**
     * Display the specified resource.
     */
    public function show(FormTemplate $formTemplate)
    {
        return new FormTemplateResource($formTemplate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFormTemplateRequest $request, FormTemplate $formTemplate)
    {
        $formTemplate->update($request->validated());
        return new FormTemplateResource($formTemplate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FormTemplate $formTemplate)
    {
        $formTemplate->deleteOrFail();
        return response()->noContent();
    }
}
