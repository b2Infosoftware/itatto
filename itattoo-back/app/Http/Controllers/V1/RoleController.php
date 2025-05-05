<?php

namespace App\Http\Controllers\V1;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\RoleRequest;
use App\Http\Resources\V1\RoleResource;
use App\Http\Resources\V1\PermissionResource;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny', Role::class);
        $roles = Role::whereOrganisationId(auth()->user()->default_organisation_id)->with('permissions')->withCount('staff')->get();

        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\V1\RoleRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RoleRequest $request)
    {
        $this->authorize('create', Role::class);
        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'organisation_id' => auth()->user()->default_organisation_id,
        ]);

        $role->permissions()->sync($request->permissions);

        return response()->json([
            'message' => trans('general.create', ['resource' => trans('resource.role')]),
            'data' => new RoleResource($role),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Role $role)
    {
        $this->authorize('view', $role);

        $role->load('permissions');

        return new RoleResource($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\V1\RoleRequest
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(RoleRequest $request, Role $role)
    {
        $this->authorize('update', $role);
        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        $role->permissions()->sync($request->permissions);

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.role')]),
            'data' => new RoleResource($role),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $role->permissions()->sync([]);
        $role->delete();

        return response()->json(['message' => trans('general.delete', ['entity' => 'role'])]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPermissions()
    {
        $permissions = auth()->user()->permissions;

        return response()->json(['data' => PermissionResource::collection($permissions)]);
    }
}
