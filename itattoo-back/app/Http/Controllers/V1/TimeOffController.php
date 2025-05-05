<?php

namespace App\Http\Controllers\V1;

use App\Models\Staff;
use App\Models\TimeOff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\TimeOffRequest;
use App\Http\Resources\V1\TimeOffResource;

class TimeOffController extends Controller
{
    /**
     * Show the values for the given resource.
     *
     * @param \App\Models\TimeOff $timeOff
     * @return \App\Http\Resources\TimeOffResource
     */
    public function show(Request $request, TimeOff $timeOff)
    {
        $this->authorize('update', $timeOff->staff);

        return new TimeOffResource($timeOff);
    }

    /**
     * Store a new entry in the database.
     *
     * @param \App\Models\TimeOff $timeOff
     * @return \App\Http\Resources\TimeOffResource
     */
    public function store(TimeOffRequest $request)
    {

        $this->authorize('viewAny', Staff::class);

        $timeOff = TimeOff::create($request->all());

        return response()->json([
            'message' => trans('general.create_successfully'),
            'data' => new TimeOffResource($timeOff),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TimeOff  $timeOff
     * @return \Illuminate\Http\Response
     */
    public function update(TimeOffRequest $request, TimeOff $timeOff)
    {
        $this->authorize('update', $timeOff->staff);

        $timeOff->update($request->all());

        return response()->json([
            'message' => trans('general.update_successfully'),
            'data' => new TimeOffResource($timeOff),
        ], 200);
    }

    /**
     * Remove the specified resource from database.
     *
     *
     * @param \App\Models\TimeOff $timeOff
     *
     * @return \App\Http\Resources\ResponseResource
     */
    public function destroy(TimeOff $timeOff)
    {
        $this->authorize('delete', $timeOff->staff);
        try {
            $timeOff->delete();

            return response()->json(['message' => trans('general.delete_successfully')]);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['message' => trans('general.error')], 500);
        }
    }
}
