<?php

namespace App\Http\Controllers\V1;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ServiceRequest;
use App\Http\Resources\V1\ServiceResource;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Service $service
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $services = Service::where('organisation_id', auth()->user()->default_organisation_id)->get();

        return ServiceResource::collection($services);
    }

    /**
     * Show the values for the given resource.
     *
     * @param  App\Http\Requests\V1\ServiceRequest $request
     * @return \App\Http\Resources\ServiceResource
     */
    public function show(Request $request, Service $service)
    {
        $this->authorize('view', $service);

        return new ServiceResource($service);
    }

    /**
     * Store a new entry in the database.
     *
     * @param  App\Http\Requests\V1\ServiceRequest $request
     * @return \App\Http\Resources\ServiceResource
     */
    public function store(ServiceRequest $request)
    {
        $this->authorize('create', Service::class);

        $service = Service::create($request->except(['staff']));

        $service->staff()->sync($request->staff);

        return response()->json([
            'message' => trans('general.create', ['resource' => trans('resource.service')]),
            'data' => new ServiceResource($service),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\V1\ServiceRequest $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(ServiceRequest $request, Service $service)
    {
        $this->authorize('update', $service);

        $service->update($request->except(['staff']));
        $service->staff()->sync($request->staff);

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.service')]),
            'data' => new ServiceResource($service),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function changeOrder(Request $request)
    {
        $this->authorize('create', Service::class);

        //TODO: Make sure the user can manage all of service ids
        foreach ($request->ids as $key => $id) {
            Service::whereId($id)->update(['position' => $request->order[$key]]);
        }

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.service')]),
            'data' => [],
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
    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);
        try {
            $service->delete();

            return response()->json(['message' => trans('general.delete', ['resource' => trans('resource.service')])]);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['message' => trans('general.error')], 500);
        }
    }
}
