<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmploymentType;
use App\Http\Requests\CreateEmploymentTypeRequest;
use App\Http\Requests\UpdateEmploymentTypeRequest;
use App\Http\Resources\EmploymentTypeResource;
use App\Models\EmploymentType;
use Illuminate\Http\Request;

class EmploymentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $employmentType = EmploymentType::select('id', 'name', 'code', 'is_active')->paginate(10);
        return EmploymentTypeResource::collection($employmentType)->additional(['message' => 'Employment Types Successfully Retrieved']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateEmploymentTypeRequest $request)
    {
        $data = EmploymentType::create($request->validated());
        return (new EmploymentTypeResource($data))->additional(['message' => 'Employment Type created']);
    }

    /**
     * Display the specified resource.
     */
    public function show(EmploymentType $employmentType)
    {
        return new EmploymentTypeResource($employmentType);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmploymentTypeRequest $request, EmploymentType $employmentType)
    {

        $employmentTypeUpdated = $employmentType->update($request->validated());

        return new EmploymentTypeResource($employmentTypeUpdated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmploymentType $employmentType)
    {
        $employmentType->deleteOrFail();
        return response()->noContent();
    }
}
