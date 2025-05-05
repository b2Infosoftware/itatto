<?php

namespace App\Http\Controllers\V1;

use App\Models\ConsentForm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ConsentFormRequest;
use App\Http\Resources\V1\ConsentFormResource;

class ConsentFormController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function index()
    {
        $consentForms = ConsentForm::inOrganisation()->with('category')->get();

        return ConsentFormResource::collection($consentForms);
    }

    /**
     * Display the specified resource.
     */
    public function forCategory(int $categoryId)
    {
        $consentForm = ConsentForm::whereCategoryId($categoryId)->active()->first();

        // exception to handle things nicer in the wizard process
        if (! $consentForm) {
            return response()->json(['data' => null]);
        }

        $this->authorize('view', $consentForm);

        return new ConsentFormResource($consentForm);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ConsentForm $consentForm)
    {
        $this->authorize('view', $consentForm);

        return new ConsentFormResource($consentForm);
    }

    /**
     * Store a new entry in the database.
     *
     * @param ConsentForm $consentForm
     * @return \App\Http\Resources\ConsentFormResource
     */
    public function store(ConsentFormRequest $request)
    {
        $this->authorize('create', ConsentForm::class);

        $consentForm = ConsentForm::create($request->all());

        return response()->json([
            'message' => trans('general.create', ['resource' => trans('resource.consent_form')]),
            'data' => new ConsentFormResource($consentForm),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\V1\ConsentFormRequest  $request
     * @param ConsentForm $consentForm
     * @return \App\Http\Resources\ConsentFormResource
     */
    public function update(ConsentFormRequest $request, ConsentForm $consentForm)
    {
        $this->authorize('update', $consentForm);

        $consentForm->update($request->all());

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.consent_form')]),
            'data' => new ConsentFormResource($consentForm),
        ], 200);
    }

    /**
     * Remove the specified resource from database.
     *
     *
     * @param ConsentForm $consentForm
     *
     * @return \App\Http\Resources\ResponseResource
     */
    public function destroy(ConsentForm $consentForm)
    {
        $this->authorize('delete', $consentForm);
        try {
            $consentForm->delete();

            return response()->json(['message' => trans('general.delete', ['resource' => trans('resource.consent_form')])]);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['message' => trans('general.error')], 500);
        }
    }
}
