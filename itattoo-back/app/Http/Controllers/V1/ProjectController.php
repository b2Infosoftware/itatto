<?php

namespace App\Http\Controllers\V1;

use App\Models\Project;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\SignedDocument;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ProjectRequest;
use App\Http\Resources\V1\ProjectResource;
use App\Http\Requests\V1\SignedDocumentRequest;
use App\Http\Resources\V1\SignedDocumentResource;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Appointment::class);
        $projects = Project::whereCustomerId($request->customer_id)->with('category')->get();

        return ProjectResource::collection($projects);
    }

    /**
     * Save a project in the database to later associate it
     * with one or more appointments
     *
     * @param  App\Http\Requests\V1\ProjectRequest $request
     * @return \App\Http\Resources\ProjectResource
     */
    public function store(ProjectRequest $request)
    {
        $this->authorize('create', Appointment::class);
        DB::beginTransaction();

        try {
            $project = Project::create($request->except('signed_document'));

            DB::commit();

            return response()->json([
                'message' => trans('general.create', ['resource' => trans('resource.project')]),
                'data' => new ProjectResource($project),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return response()->json(['message' => trans('general.error')], 500);
        }
    }

    /**
     *  Update the specified resource in storage.
     *
     * @param  App\Http\Requests\V1\ProjectRequest $request
     * @return \App\Http\Resources\ProjectResource
     */
    public function update(ProjectRequest $request, Project $project)
    {
        if ($project->staff_id != auth()->user()->id) {
            abort(403);
        }
        $project->update($request->all());

        // handle other stuff related to project like documents and whatnot

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.project')]),
            'data' => new ProjectResource($project),
        ], 200);
    }

    /**
     * Remove the specified resource from database.
     *
     *
     * @param \App\Models\Project $project
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Project $project)
    {
        if ($project->staff_id != auth()->user()->id) {
            abort(403);
        }
        try {
            if ($project->appointments()->upcoming()->exists()) {
                return response()->json(['message' => trans('general.error')], 500);
            }

            $project->delete();

            return response()->json(['message' => trans('general.delete', ['resource' => trans('resource.project')])]);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['message' => trans('general.error')], 500);
        }
    }

    /**
     *  Saves a signed document and associates it with the project.
     *
     * @param  App\Http\Requests\V1\DocumentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveDocument(SignedDocumentRequest $request)
    {
        $document = SignedDocument::generateFromRequest();

        return response()->json([
            'message' => trans('general.create', ['resource' => trans('resource.project')]),
            'data' => new SignedDocumentResource($document),
        ]);
    }
}
