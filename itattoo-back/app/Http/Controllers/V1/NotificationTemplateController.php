<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Http\Requests\V1\NotificationTemplateRequest;
use App\Http\Resources\V1\NotificationTemplateResource;

class NotificationTemplateController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', NotificationTemplate::class);

        $notificationTemplates = NotificationTemplate::whereOrganisationId(auth()->user()->default_organisation_id)->get();

        return NotificationTemplateResource::collection($notificationTemplates);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, NotificationTemplate $notificationTemplate)
    {
        $this->authorize('view', $notificationTemplate);

        return new NotificationTemplateResource($notificationTemplate);
    }

    /**
     * Store a new entry in the database.
     *
     * @param NotificationTemplate $notificationTemplate
     * @return \App\Http\Resources\NotificationTemplateResource
     */
    public function store(NotificationTemplateRequest $request)
    {
        $this->authorize('create', NotificationTemplate::class);

        $request->merge([
            'organisation_id' => auth()->user()->default_organisation_id,
        ]);
        $notificationTemplate = NotificationTemplate::create($request->all());

        return response()->json([
            'message' => trans('general.create', ['resource' => trans('resource.email')]),
            'data' => new NotificationTemplateResource($notificationTemplate),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\V1\NotificationTemplateRequest  $request
     * @param NotificationTemplate $notificationTemplate
     * @return \App\Http\Resources\NotificationTemplateResource
     */
    public function update(NotificationTemplateRequest $request, NotificationTemplate $notificationTemplate)
    {
        $this->authorize('update', $notificationTemplate);

        $notificationTemplate->update($request->all());

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.email')]),
            'data' => new NotificationTemplateResource($notificationTemplate),
        ], 200);
    }

    /**
     * Remove the specified resource from database.
     *
     *
     * @param NotificationTemplate $notificationTemplate
     *
     * @return \App\Http\Resources\ResponseResource
     */
    public function destroy(NotificationTemplate $notificationTemplate)
    {
        $this->authorize('delete', $notificationTemplate);

        try {
            $notificationTemplate->delete();

            return response()->json(['message' => trans('general.delete', ['resource' => trans('resource.email')])]);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['message' => trans('general.error')], 500);
        }
    }
}
