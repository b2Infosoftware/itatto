<?php

namespace App\Http\Controllers\V1;

use App\Models\Discount;
use Illuminate\Http\Request;
use App\Models\ExclusiveOffer;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ExclusiveOfferRequest;
use App\Http\Resources\V1\ExclusiveOfferResource;

class ExclusiveOfferController extends Controller
{
    public function index()
    {
        $ExclusiveOffer = auth()->user()->defaultLocation->exclusiveOffers;
        return ExclusiveOfferResource::collection($ExclusiveOffer);
    }

    public function store(ExclusiveOfferRequest $request)
    {
        $request->validated();
        $exclusiveOffer = ExclusiveOffer::create($request->only(
            'offer_name',
            'offer_details',
            'start_date',
            'end_date',
            'location_id'
        ));

        $exclusiveOffer->discount()->create($request->only('discount_type', 'discount_value'));
        $exclusiveOffer->vips()->sync($request->vips);
        return new ExclusiveOfferResource($exclusiveOffer->load('discount', 'vips'));
    }

    public function show(ExclusiveOffer $exclusiveOffer)
    {
        return new ExclusiveOfferResource($exclusiveOffer);
    }

    public function update(ExclusiveOfferRequest $request, ExclusiveOffer $exclusiveOffer)
    {
        $exclusiveOffer->update($request->only('offer_name', 'offer_details', 'start_date', 'end_date'));
        $exclusiveOffer->discount()->update($request->only('discount_type', 'discount_value'));
        $exclusiveOffer->vips()->sync($request->vips);

        return new ExclusiveOfferResource($exclusiveOffer->load('discount', 'vips'));
    }

    public function destroy(ExclusiveOffer $exclusiveOffer)
    {
        $exclusiveOffer->delete();
        return response()->json(['message' => 'Exclusive Offer deleted successfully']);
    }
}
