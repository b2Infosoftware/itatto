<?php

namespace App\Http\Controllers\V1;

use Carbon\Carbon;
use App\Models\Staff;
use App\Mail\SlotMail;
use App\Models\Customer;
use App\Models\PublicSlot;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\V1\CustomerResource;
use App\Http\Resources\V1\PublicSlotResource;
use App\Http\Resources\V1\AppointmentResource;

class ManageBookingController extends Controller
{
    public function bookingList(Request $request){
        $getId = Auth::id();
        $getOrganisationsId = Staff::with('organisations')->find($getId)->default_organisation_id;
        $perPage = $request->query('per_page', 10);
        $data = PublicSlot::with('service', 'staff')
                    ->where('organisations_id', $getOrganisationsId)
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);

        return response()->json($data);
    }

    public function approve(PublicSlot $publicSlot)
    {
        $user = Auth::user();
        $publicSlot->update([
            'status' => '1',
            'status_updated_by' => $user->id,
        ]);

        $appointment = Appointment::updateOrCreate(
            [
                'customer_id' => $publicSlot->customer_id,
                'service_id' => $publicSlot->service_id,
            ],
            [
                'date' => $publicSlot->date,
                'start_time' => $publicSlot->start_time,
                'end_time' => $publicSlot->end_time,
                'organisation_id' => $publicSlot->organisations_id,
                'staff_id' => $publicSlot->staff_id,
                'status' => 'not_presented',
                'price' => $publicSlot->service->price,
                'duration' => $publicSlot->service->duration,
                'location_id' => $publicSlot->organisations_id,
            ]
        );

        return response()->json([
            'message' => 'Booking has been approved and appointment created.',
            'publicSlot' => $publicSlot,
            'appointment' => new AppointmentResource($appointment),
        ]);
    }

    public function rejected(PublicSlot $publicSlot)
    {
        $user = Auth::user()->id;

        $publicSlot->update([
            'status' => '2',
            'status_updated_by' => $user
        ]);

        Mail::to($publicSlot->email)->send(new SlotMail($publicSlot, 'rejected'));

        return response()->json([
            'message' => 'Booking has been rejected and email sent.',
            'publicSlot' => $publicSlot,
        ]);
    }

    public function reschedule(Request $request, PublicSlot $publicSlot)
    {
        $user = Auth::user()->id;

        $validated = $request->validate([
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $publicSlot->update([
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => '1',
            'is_reschedule' => 1,
            'status_updated_by' => $user,
        ]);

        $appointment = Appointment::withoutEvents(function () use ($publicSlot) {
            return Appointment::updateOrCreate(
                [
                    'customer_id' => $publicSlot->customer_id,
                    'service_id' => $publicSlot->service_id,
                ],
                [
                    'date' => $publicSlot->date,
                    'start_time' => $publicSlot->start_time,
                    'end_time' => $publicSlot->end_time,
                    'organisation_id' => $publicSlot->organisations_id,
                    'staff_id' => $publicSlot->staff_id,
                    'status' => 'not_presented',
                    'price' => $publicSlot->service->price,
                    'duration' => $publicSlot->service->duration,
                    'location_id' => $publicSlot->organisations_id,
                ]
            );
        });

        Mail::to($publicSlot->email)->send(new SlotMail($publicSlot, 'reschedule'));

        return response()->json([
            'message' => 'Booking has been rescheduled and appointment updated.',
            'publicSlot' => $publicSlot,
            'appointment' => new AppointmentResource($appointment),
        ]);
    }

    public function destroy(PublicSlot $publicSlot)
    {
        DB::beginTransaction();
        try {
            $publicSlot->delete();
            $customer = Customer::find($publicSlot->customer_id);
            $customer->delete();
            DB::commit();
            return response()->json(['message' => 'Customer and PublicSlot deleted successfully'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }
}
