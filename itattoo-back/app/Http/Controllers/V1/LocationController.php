<?php

namespace App\Http\Controllers\V1;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LocationRequest;
use App\Http\Resources\V1\LocationResource;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Location $location
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny', Location::class);

        if (auth()->user()->isSuperAdmin() || auth()->user()->defaultOrganisation->owner_id == auth()->user()->id) {
            $locations = auth()->user()->defaultOrganisation->locations()->get();
        } else {
            $locations = auth()->user()->locations()->get();
        }

        return LocationResource::collection($locations);
    }

    /**
     * Store a new location in the database.
     *
     * @param Location $location
     * @return \App\Http\Resources\LocationResource
     */
    public function store(LocationRequest $request)
    {
        $this->authorize('create', Location::class);

        $location = Location::create($request->all());

        return response()->json([
            'message' => trans('general.create', ['resource' => trans('resource.location')]),
            'data' => new LocationResource($location),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(LocationRequest $request, Location $location)
    {
        $this->authorize('update', $location);

        $location->update($request->except('id', 'country', 'organisation_id'));

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.location')]),
            'data' => new LocationResource($location),
        ], 200);
    }

    /**
     * Remove the specified resource from database.
     *
     *
     * @param Location $location
     *
     * @return \App\Http\Resources\ResponseResource
     */
    public function destroy(Location $location)
    {
        $this->authorize('delete', $location);

        try {
            $location->delete();

            return response()->json(['message' => trans('general.delete', ['resource' => trans('resource.location')])]);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['message' => trans('general.error')], 500);
        }
    }
}
