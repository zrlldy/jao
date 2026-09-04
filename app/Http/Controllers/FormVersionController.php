<?php

namespace App\Http\Controllers;


use App\Http\Requests\CreateFormVersionRequest;
use App\Http\Resources\FormVersionResource;
use App\Models\FormVersion;
use Illuminate\Http\Request;

class FormVersionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formVersion = FormVersion::with('formTemplate')->select('id', 'published_at', 'version', 'status')->paginate(10);

        return FormVersionResource::collection($formVersion);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateFormVersionRequest $request)
    {
        $formVersion = FormVersion::create($request->validated());

        return new FormVersionResource($formVersion);
    }

    /**
     * Display the specified resource.
     */
    public function show(FormVersion $formVersion)
    {
        return new FormVersionResource($formVersion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FormVersion $formVersion)
    {
        $formVersion->update($request->validated());
        return new FormVersionResource($formVersion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FormVersion $formVersion)
    {
        $formVersion->delete();
        return response()->noContent();
    }
}
