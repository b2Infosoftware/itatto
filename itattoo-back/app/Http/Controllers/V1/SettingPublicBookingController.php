<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\SettingPublicBookingRequest;
use App\Http\Resources\V1\SettingPublicBookingResource;
use App\Models\Organisation;
use App\Models\SettingPublicBooking;
use Illuminate\Support\Facades\Auth;

class SettingPublicBookingController extends Controller
{
    public function show()
    {
        $getId = Auth::user()->default_organisation_id;
        $data = SettingPublicBooking::with('organisation', 'media')->where('organisation_id', $getId)->first();
        
        return response()->json( new SettingPublicBookingResource($data));
    }


    public function createOrUpdate(SettingPublicBookingRequest $request)
    {
        $settingPublicBooking = SettingPublicBooking::with('organisation', 'media')->updateOrCreate(
            ['organisation_id' => $request->organisation_id],
            $request->all()
        );

        $organisationId = $settingPublicBooking->organisation->id;
        Organisation::updateOrCreate(
            ['id' => $organisationId], 
            ['description' => $request->description] 
        );

        return response()->json(new SettingPublicBookingResource($settingPublicBooking), 200);
    }
}