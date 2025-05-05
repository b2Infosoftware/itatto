<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Models\CalendarSettings;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CalendarSettingsResource;

class CalendarSettingsController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show()
    {
        $calendarSettings = CalendarSettings::whereOrganisationId(auth()->user()->default_organisation_id)->first();

        $this->authorize('view', $calendarSettings);

        return new CalendarSettingsResource($calendarSettings);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CalendarSettings $calendarSettings)
    {
        $this->authorize('update', $calendarSettings);

        $calendarSettings->update($request->all());
        $calendarSettings->refresh();

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.calendar_setting')]),
            'data' => new CalendarSettingsResource($calendarSettings),
        ], 200);
    }
}
