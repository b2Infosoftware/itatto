<?php

namespace App\Http\Controllers\V1;

use Carbon\Carbon;
use App\Models\Log;
use App\Models\TimeOff;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\AppointmentRequest;
use App\Http\Resources\V1\AppointmentResource;
use Illuminate\Support\Facades\Log as FacadesLog;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Appointment::class);

        $appointments = Appointment::filtered()->get();

        return AppointmentResource::collection($appointments);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchUpcoming(Request $request)
    {
        $this->authorize('viewAny', Appointment::class);

        $appointments = Appointment::filtered(true)->searchByCustomerInfo($request->customer_name)->take(20)->get();

        return AppointmentResource::collection($appointments);
    }

    /**
     * Show the values for the given resource.
     *
     * @param Appointment $appointment
     * @return \App\Http\Resources\AppointementResource
     */
    public function show(Appointment $appointment)
    {
        $this->authorize('view', Appointment::class);

        return new AppointmentResource($appointment);
    }

    /**
     * Store a new entry in the database.
     *
     * @param  App\Http\Requests\V1\AppointmentRequest $request
     * @return \App\Http\Resources\AppointmentResource
     */
    public function store(AppointmentRequest $request)
    {
        $this->authorize('create', Appointment::class);

        if ($request->organisation_id != auth()->user()->default_organisation_id) {
            abort(403, trans('validation.not_enough_permissions'));
        }
        if ($request->staff_id != auth()->user()->id && !auth()->user()->hasAccessTo('manage others', 'appointments')) {
            abort(403, trans('validation.not_enough_permissions'));
        }

        DB::beginTransaction();

        try {
            $appointment = Appointment::create($request->except(['signed_document']));

            $vip = $appointment->customer->vip;
            if ($vip) {
                $activeOffers = $vip->computeDiscountedPrice($request->price, false);
                $appointment->update(['price' => $activeOffers]);
            }

            if ($request->signed_document) {
                $appointment->document()->create([
                    'path' => $request->signed_document,
                ]);
            }

            DB::commit();

            Log::addLog('appointment_created', $appointment);

            return response()->json([
                'message' => trans('general.create', ['resource' => trans('resource.appointment')]),
                'data' => new AppointmentResource($appointment),
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
     * @param  App\Http\Requests\V1\AppointmentRequest $request
     * @return \App\Http\Resources\AppointmentResource
     */
    public function update(AppointmentRequest $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        $appointment->update($request->except('send_notification'));

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.appointment')]),
            'data' => new AppointmentResource($appointment),
        ], 200);
    }

    /**
     *  Change the status of ana ppointment.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\Http\Resources\AppointmentResource
     */
    public function changeStatus(Request $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        $appointment->update(['status' => $request->status]);

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.appointment')]),
            'data' => new AppointmentResource($appointment),
        ], 200);
    }

    /**
     *  Duplicate an appointment.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\Http\Resources\AppointmentResource
     */
    public function duplicate(Request $request, Appointment $appointment)
    {
        $this->authorize('create', Appointment::class);
        $organisation = Organisation::find(auth()->user()->default_organisation_id);
        $overlapped = 0;
        foreach ($request->dates as $new) {
            $end_time = Carbon::parse($new['start_time'])->addMinutes($new['duration'])->format('H:i');
            $overlaps = Appointment::withoutGlobalScopes()
                ->whereOrganisationId($organisation->id)
                ->whereLocationId($appointment->location_id)
                ->whereStaffId($appointment->staff_id)
                ->whereDate('date', $new['date'])
                ->whereTime('start_time', '<', $end_time)
                ->whereTime('end_time', '>', $new['start_time'])
                ->exists();

            if ($overlaps && ! $organisation->calendarSettings->allow_double_booking) {
                $overlapped++;
                continue;
            }

            if (! $organisation->calendarSettings->allow_off_hours_booking) {
                $day = Carbon::parse($new['date'])->weekDay();
                if ($day == 0) {
                    $day = 7;
                }
                $break = Availability::whereStaffId($appointment->staff_id)
                    ->whereLocationId($appointment->location_id)
                    ->whereIsAvailable(0)
                    ->where('day', $day)
                    ->whereTime('start_time', '<', $end_time)
                    ->whereTime('end_time', '>', $new['start_time'])
                    ->exists();
                if ($break) {
                    $overlapped++;
                    continue;
                }

                $timeOff = TimeOff::whereStaffId($appointment->staff_id)
                    ->whereDate('start_date', '<', $end_time)
                    ->whereDate('end_date', '>', $new['start_time'])
                    ->exists();

                if ($timeOff) {
                    $overlapped++;
                    continue;
                }
            }

            Appointment::create([
                'staff_id' => $appointment->staff_id,
                'customer_id' => $appointment->customer_id,
                'location_id' => $appointment->location_id,
                'service_id' => $appointment->service_id,
                'price' => $appointment->price,
                'organisation_id' => auth()->user()->default_organisation_id,
                'project_id' => $appointment->project_id,
                'date' => $new['date'],
                'start_time' => $new['start_time'],
                'end_time' => $end_time,
                'duration' => $new['duration'],
                'note' => $new['note'],
            ]);
        }

        if ($overlapped == count($request->dates)) {
            abort(403, trans('validation.custom.no_double_booking'));
        }

        return response()->json([
            'message' => trans('general.create', ['resource' => trans('resource.appointment')]),
            'data' => new AppointmentResource($appointment),
        ], 200);
    }

    /**
     *  Allows even an unauthenticated user to cancel an appointment
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\Http\Resources\AppointmentResource
     */
    public function cancel(Request $request, int $id)
    {
        $request->validate([
            'email' => 'required|email|unique:staff,email',
        ]);
        $appointment = Appointment::withoutGlobalScopes()->findOrFail($id);

        if ($request->email != $appointment->customer->email) {
            abort(403, trans('general.not_your_appointment'));
        }

        $org = Organisation::findOrFail($appointment->organisation_id);
        $appDate = Carbon::parse($appointment->date . ' ' . $appointment->start_time);

        if ($appDate->isPast()) {
            abort(403, trans('general.too_late_to_cancel'));
        }
        if (now()->diffInDays($appDate) < $org->cancellation_buffer_days) {
            abort(403, trans('general.too_late_to_cancel'));
        }

        $appointment->update(['status' => 'canceled']);

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.appointment')]),
        ], 200);
    }

    /**
     * Cancels an appointment.
     *
     *
     * @param \App\Models\Appointment $appointment
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Appointment $appointment)
    {
        $this->authorize('delete', $appointment);

        try {
            $appointment->delete();

            return response()->json(['message' => trans('general.delete', ['resource' => trans('resource.appointment')])]);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['message' => trans('general.error')], 500);
        }
    }
}
