<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OnlineHourResource;
use App\Models\OnlineHour;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnlineHourController extends Controller
{
    public function syncOnlineHours(Request $request)
    {
        $data = $request->all();
        $staffId = $data[0]['staff_id'] ?? null;

        if (!$staffId) {
            return response()->json(['message' => 'Staff ID is required'], 400);
        }

        $keptOnlineHourIds = [];
        $syncedOnlineHours = [];

        foreach ($data as $item) {
            $onlineHour = OnlineHour::updateOrCreate(
                [
                    'staff_id' => $item['staff_id'],
                    'organisation_id' => $item['organisation_id'],
                    'location_id' => $item['location_id'],
                    'start_time' => $item['start_time'],
                    'end_time' => $item['end_time'],
                    'day' => $item['day']
                ],
                [
                    'is_available' => $item['is_available']
                ]
            );

            $keptOnlineHourIds[] = $onlineHour->id;

            $onlineServices = DB::table('service_staff')
                ->whereIn('service_id', $item['service'])
                ->where('staff_id', $staffId)
                ->where('is_online', true)
                ->pluck('service_id')
                ->toArray();

            $onlineHour->services()->sync($onlineServices);

            $syncedOnlineHours[] = new OnlineHourResource($onlineHour);
        }
        OnlineHour::where('staff_id', $staffId)
            ->whereNotIn('id', $keptOnlineHourIds)
            ->delete();

        return response()->json([
            'message' => 'Online hours synced successfully',
            'data' => OnlineHourResource::collection($syncedOnlineHours)
        ], 200);
    }

    public function store(Request $request)
    {
        $result = OnlineHour::create($request->only(['staff_id', 'organisation_id', 'location_id', 'start_time', 'end_time', 'day', 'is_available']));
        return response()->json([
            'message' => 'Online hour created successfully',
            'data' => new OnlineHourResource($result)
        ], 201);
    }


    public function getOnlineHoursByStaff($staffId)
    {
        $staff = Staff::with('onlineHours.services')->find($staffId);

        if (!$staff) {
            return response()->json(['message' => 'Staff not found'], 404);
        }

        return response()->json(OnlineHourResource::collection($staff->onlineHours));
    }
}
