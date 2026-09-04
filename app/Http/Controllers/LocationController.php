<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $location = Location::select('id', 'name', 'code', 'address', 'province', 'hub', 'is_active')->paginate(10);

        return LocationResource::collection($location)->additional(['message' => 'Location successfully retrieved']);
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateLocationRequest $request)
    {
        $location = Location::create($request->validated());
        return (new LocationResource($location))->additional(['message' => 'Location created successfully!']);
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {

        return new LocationResource($location);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, Location $location)
    {
        $locationUpdated = $location->update($request->validated());
        return new LocationResource($locationUpdated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        $location->delete();
        return response()->noContent();
    }
}
