<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\TagRequest;
use App\Http\Resources\V1\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Tag $tag
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tags = Tag::where('organisation_id', auth()->user()->default_organisation_id)->get();
        return TagResource::collection($tags);
    }

    /**
     * Store a new entry in the database.
     *
     * @param \App\Models\Tag $tag
     * @return \App\Http\Resources\TagResource
     */
    public function store(TagRequest $request)
    {
        $tag = Tag::create([
            'name' => $request->name,
            'organisation_id' => auth()->user()->default_organisation_id
        ]);

        return response()->json([
            'message' => trans('general.create', ['resource' => trans('resource.tag')]),
            'data' => new TagResource($tag)
        ], 200);
    }

    /**
     * Remove the specified resource from database.
     *
     *
     * @param \App\Models\Tag $tag
     *
     * @return \App\Http\Resources\ResponseResource
     *
     */
    public function destroy(Tag $tag)
    {
        try {
            if($tag->staff()->exists()) {
                $tag->staff()->detach();
            }
        
            $tag->delete();

            return response()->json(['message' => trans('general.delete', ['resource' => trans('resource.tag')])]);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => trans('general.error')], 500);
        }
    }
}
