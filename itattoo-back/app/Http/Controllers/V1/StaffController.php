<?php

namespace App\Http\Controllers\V1;

use App\Models\Staff;
use Illuminate\Support\Str;
use App\Events\StaffCreated;
use App\Models\Organisation;
use Illuminate\Http\Request;
use App\Events\InvitationAccepted;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StaffRequest;
use App\Http\Resources\V1\StaffResource;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Staff $staff
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Staff::class);

        $staff = auth()->user()->defaultOrganisation->staff()->with('role')->inLocation($request->location_id)->get();

        return StaffResource::collection($staff);
    }

    /**
     * Show the values for the given resource.
     *
     * @param \App\Models\Staff $staff
     * @return \App\Http\Resources\StaffResource
     */
    public function show(Request $request, Staff $staff)
    {
        $this->authorize('view', $staff);

        $staff->load(['availability', 'role', 'timeOff', 'services', 'locations', 'completedAppointments', 'onlineHours']);

        return new StaffResource($staff);
    }

    /**
     * Store a new staff member in the database.
     *
     * @param \App\Models\Staff $staff
     * @return \App\Http\Resources\StaffResource
     */
    public function store(StaffRequest $request)
    {
        $this->authorize('create', Staff::class);

        $organisation = Organisation::whereId(auth()->user()->defaultOrganisation->id)->firstOrFail();

        $staff = Staff::where('email', $request->email)->first();

        if (! $staff) {
            // create new staff
            $staff = Staff::create($request->except(['tag_ids', 'location_ids', 'service_ids']));

            $staff->tags()->syncWithoutDetaching($request->tag_ids);
            $staff->locations()->syncWithoutDetaching($request->location_ids);
            $staff->services()->syncWithoutDetaching($request->service_ids);
            $staff->generateAvailability();

            $this->associateStaffWithOrganisation($staff, $organisation, $request->role_id);

            return response()->json([
                'message' => trans('general.create', ['resource' => trans('resource.staff')]),
                'data' => new StaffResource($staff),
            ], 200);
        }

        $staff->locations()->syncWithoutDetaching($request->location_ids);
        $staff->services()->syncWithoutDetaching($request->service_ids);
        $this->associateStaffWithOrganisation($staff, $organisation, $request->role_id);

        $staff->generateAvailability();

        return response()->json([
            'message' => trans('general.send_invitation', ['email' => $request->email]),
        ], 200);
    }

    /**
     * Associate staff member with organisation
     *
     * @param \App\Models\Staff $staff
     * @param \App\Models\Organisation $organisation
     */
    public function associateStaffWithOrganisation($staff, $organisation, $roleId)
    {
        // attach organisation to user
        $staff->organisations()->attach(auth()->user()->defaultOrganisation->id, ['role_id'=>$roleId]);

        // send email
        event(new StaffCreated($staff, $organisation));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\V1\StaffRequest  $request
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function update(StaffRequest $request, Staff $staff)
    {
        $this->authorize('update', $staff);

        if($staff->organisations()->count() == 1 || $staff->if == auth()->user()->id){
            $staff->update($request->except(['tag_ids', 'location_ids', 'service_ids', 'role_id']));
        }

        $orgId = auth()->user()->default_organisation_id;


        //locations and services and tags
        $ids = $staff->locations()->whereOrganisationId($orgId)->pluck('location_id')->all();
        $staff->locations()->detach($ids);
        $staff->locations()->attach($request->location_ids);

        $ids = $staff->tags()->whereOrganisationId($orgId)->pluck('tag_id')->all();
        $staff->tags()->detach($ids);
        $staff->tags()->attach($request->tag_ids);

        $ids = $staff->services()->whereOrganisationId($orgId)->pluck('service_id')->all();
        $staff->services()->detach($ids);
        $staff->services()->attach($request->service_ids);

        // dealing with role
        $org = $staff->organisations()->whereOrganisationId($orgId)->first();
        $org->pivot->role_id = $request->role_id;
        $org->pivot->save();

        if ($staff->default_organisation_id == $orgId) {
            $staff->update(['role_id' => $request->role_id]);
        }

        $staff->generateAvailability();

        $staff->load(['availability', 'timeOff', 'services', 'locations']);

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.staff')]),
            'data' => new StaffResource($staff),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function syncServices(Request $request, Staff $staff)
    {
        $this->authorize('syncServices', $staff);

        if (! count($request->service_ids)) {
            return abort(422, trans('validation.custom.min_one_service'));
        }

        $serviceData = [];
        foreach ($request->service_ids as $service) {
            $serviceData[$service['id']] = ['is_online' => $service['is_online'] ?? 0];
        }

        $staff->services()->sync($serviceData);

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.staff')]),
        ], 200);
    }

    /**
     * Accept invitation: update relationship in db, generate password and send the email to the new user created
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function acceptInvitation(Request $request)
    {
        if (! $request->hasValidSignature()) {
            return response()->json([
                'message' => trans('auth.broken_verification_link'),
            ], 403);
        }

        $staff = Staff::whereId($request->id)->firstOrFail();

        $staffOrg = $staff->organisations()->whereOrganisationId($request->organisation_id)->first();

        if (! $staff->email_verified_at) {
            $staff->update(['email_verified_at' => now()]);
        }

        // has been clicked already
        if (! is_null($staffOrg->pivot->confirmed_at)) {
            return;
        }

        $staff->organisations()->updateExistingPivot($request->organisation_id, ['confirmed_at' => now()]);

        // more than 1 org so it has a password already...
        if ($staff->organisations()->count() > 1) {
            return;
        }

        // generate password and sent him an welcome email.
        $password = Str::random(10);
        $staff->update([
            'password' => $password,
        ]);

        event(new InvitationAccepted($staff, $password));
    }

    /**
     * Resend invitation
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function resendInvitation(Request $request)
    {
        $organisation = Organisation::whereId(auth()->user()->defaultOrganisation->id)->firstOrFail();
        $staff = Staff::where('email', $request->email)->firstOrFail();

        // send email
        event(new StaffCreated($staff, $organisation));

        return response()->json([
            'message' => trans('general.send_invitation', ['email' => $request->email]),
        ], 200);
    }

    /**
     * Remove the specified resource from database.
     *
     *
     * @param \App\Models\Staff $staff
     *
     * @return \App\Http\Resources\ResponseResource
     */
    public function destroy(Staff $staff)
    {
        $this->authorize('delete', $staff);

        try {
            $staff->delete();

            return response()->json(['message' => trans('general.delete', ['resource' => trans('resource.staff')])]);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['message' => trans('general.error')], 500);
        }
    }
}
