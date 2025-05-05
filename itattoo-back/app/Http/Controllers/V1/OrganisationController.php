<?php

namespace App\Http\Controllers\V1;

use App\Models\Organisation;
use App\Http\Controllers\Controller;
use App\Models\NotificationSettings;
use App\Http\Requests\V1\OrganisationRequest;
use App\Http\Resources\V1\OrganisationResource;
use App\Http\Requests\V1\NotificationSettingsRequest;
use App\Http\Resources\V1\NotificationSettingsResource;

class OrganisationController extends Controller
{
    /**
     * Show the values for the given resource.
     *
     * @param \App\Models\Organisation $organisation
     * @return \App\Http\Resources\OrganisationResource
     */
    public function index()
    {
        if(auth()->user()->isSuperAdmin()){
            $organisations = Organisation::whereId(auth()->user()->default_organisation_id)->with('activeSubscription', 'notificationSettings', 'language')->get();
        } else {
            $organisations = auth()->user()->organisations()->with('activeSubscription', 'notificationSettings', 'language')->get();
        }

        return OrganisationResource::collection($organisations);
    }

    /**
     * Shows a list with ALL organisations so admins can view     *
     */
    public function superAdminOrganisations()
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        $organisations = Organisation::with('activeSubscription.plan', 'owner')->get();

        return response()->json(['data' => $organisations]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function update(OrganisationRequest $request, Organisation $organisation)
    {
        if (! auth()->user()->hasAccessTo('edit', 'settings')) {
            abort(403);
        }
        $organisation->update($request->all());

        $organisation->load('locations', 'activeSubscription', 'language', 'notificationSettings');

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.organisation')]),
            'data' => new OrganisationResource($organisation),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\V1\NotificationSettingsRequest  $request
     * @param  \App\Models\NotificationSettings  $settings
     * @return \Illuminate\Http\Response
     */
    public function updateNotificationSettings(NotificationSettingsRequest $request, NotificationSettings $notificationSettings)
    {
        if (! auth()->user()->hasAccessTo('edit', 'notifications')) {
            abort(403);
        }

        $notificationSettings->update($request->except('id'));
        $notificationSettings->refresh();

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.data')]),
            'data' => new NotificationSettingsResource($notificationSettings),
        ], 200);
    }


    /**
     * Remove the specified resource from database.
     *
     *
     * @param Organisation $organisation
     *
     * @return \App\Http\Resources\ResponseResource
     */
    public function suspend(Organisation $organisation)
    {
        if(!auth()->user()->isSuperAdmin()){
            abort(403, trans('general.permission_denied'));
        }
        if($organisation->suspended_at){
            $organisation->update(['suspended_at' => null]);
        } else {
            $organisation->update(['suspended_at' => now()]);
        }
        
        return response()->json(['message' => trans('general.update', ['resource' => trans('resource.organisation')])]);
    }
}
