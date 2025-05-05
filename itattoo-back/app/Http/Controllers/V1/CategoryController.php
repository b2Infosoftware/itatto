<?php

namespace App\Http\Controllers\V1;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CategoryRequest;
use App\Http\Resources\V1\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Category $category
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::where('organisation_id', auth()->user()->default_organisation_id)->get();
        $categories->load('services');

        return CategoryResource::collection($categories);
    }

    /**
     * Store a new entry in the database.
     *
     * @param \App\Models\Category $category
     * @return \App\Http\Resources\CategoryResource
     */
    public function store(CategoryRequest $request)
    {
        $this->authorize('create', Category::class);

        $category = Category::create([
            'name' => $request->name,
            'organisation_id' => auth()->user()->default_organisation_id,
        ]);

        return response()->json([
            'message' => trans('general.create', ['resource' => trans('resource.category')]),
            'data' => new CategoryResource($category),
        ], 200);
    }

    /**
     * Remove the specified resource from database.
     *
     *
     * @param \App\Models\Category $category
     *
     * @return \App\Http\Resources\ResponseResource
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        try {
            $category->delete();

            return response()->json(['message' => trans('general.delete', ['resource' => trans('resource.category')])]);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['message' => trans('general.error')], 500);
        }
    }
}
