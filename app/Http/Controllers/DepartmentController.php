<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use PHPUnit\Logging\OpenTestReporting\Status;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $department = Department::select('name', 'code', 'is_active', 'id')->paginate(10);

        if ($department->isEmpty()) {

            return response()->json(['No Department data found'], 200);
        }
        return  DepartmentResource::collection($department)->additional(['message' => 'Departments successfully retrieved']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDepartmentRequest $request)
    {
        $data = $request->validated();
        $department = Department::create([
            'name' => $data['name'],
            'code' => $data['code'],
            'is_active' => $data['is_active']
        ]);
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
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $oldname = $department->name;
        $department->update($request->validated());
        return (new DepartmentResource($department))->additional(['message' => "{$oldname} has been updated "]);
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
