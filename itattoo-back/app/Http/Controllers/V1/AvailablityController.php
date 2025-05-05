<?php

namespace App\Http\Controllers\V1;

use App\Models\Staff;
use App\Models\Availability;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\AvailabilityRequest;
use App\Http\Resources\V1\AvailablityResource;

class AvailablityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Location $location
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Staff::class);

        $working_hours = Availability::where('organisation_id', auth()->user()->default_organisation_id)->where('staff_id', $request->staff_id)->get();

        return AvailablityResource::collection($working_hours);
    }

    /**
     * Store a new entry in the database.
     *
     * @param \App\Models\Availability $availability
     * @return \App\Http\Resources\AvailabilityResource
     */
    public function store(AvailabilityRequest $request)
    {
        $this->authorize('create', Staff::class);

        $availability = Availability::create($request->all());
        $availability->refresh();

        return response()->json([
            'message' => trans('general.create_successfully'),
            'data' => new AvailablityResource($availability),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Availability  $availability
     * @return \Illuminate\Http\Response
     */
    public function update(AvailabilityRequest $request, Availability $availability)
    {
        $this->authorize('update', $availability->staff);

        $availability->update($request->all());

        return response()->json([
            'message' => trans('general.update_successfully'),
            'data' => new AvailablityResource($availability),
        ], 200);
    }

    /**
     * Remove the specified resource from database.
     *
     *
     * @param \App\Models\Service $service
     *
     * @return \App\Http\Resources\ResponseResource
     */
    public function destroy(Availability $availability)
    {
        $this->authorize('delete', $availability->staff);

        try {
            $availability->delete();

            return response()->json(['message' => trans('general.delete_successfully')]);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['message' => trans('general.error')], 500);
        }
    }
}
