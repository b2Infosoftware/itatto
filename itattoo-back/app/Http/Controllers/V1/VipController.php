<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\VipRequest;
use App\Http\Resources\V1\VipResource;
use App\Models\Customer;
use App\Models\Vip;
use Illuminate\Http\Request;

class VipController extends Controller
{
    public function index()
    {
        $vips = auth()->user()->defaultLocation->vips;
        return VipResource::collection($vips);
    }

    public function store(VipRequest $request)
    {
        $vip = Vip::create($request->only('label', 'location_id', 'color'));
        return new VipResource($vip);
    }

    public function show(Vip $vip)
    {
        return new VipResource($vip);
    }

    public function update(VipRequest $request, Vip $vip)
    {
        $vip->update($request->only('label', 'color'));
        return new VipResource($vip);
    }

    public function destroy(Vip $vip)
    {
        Customer::where('vip_id', $vip->id)->update(['vip_id' => null]);

        $vip->delete();
        return response()->json(['message' => 'Vip deleted successfully']);
    }
}
