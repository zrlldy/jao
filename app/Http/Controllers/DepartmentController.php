<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;


class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::select(
            'id',
            'name',
            'code',
            'is_active'
        )->paginate(10);


        return DepartmentResource::collection($departments)
            ->additional([
                'message' => 'Departments successfully retrieved',
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDepartmentRequest $request)
    {
        $department = Department::create($request->validated());
        return (new DepartmentResource($department))->additional(['message' => "{$department->name} successfully created"]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {

        return (new DepartmentResource($department))->additional(['message' => "{$department->name} successfully retrieved"]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateDepartmentRequest $request,
        Department $department
    ) {
        $department->update($request->validated());

        return (new DepartmentResource($department))
            ->additional([
                'message' => "{$department->name} successfully updated",
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->deleteOrFail();
        return response()->noContent();
    }
}
