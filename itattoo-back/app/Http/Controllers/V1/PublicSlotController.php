<?php

namespace App\Http\Controllers\V1;

use Carbon\Carbon;
use App\Models\Staff;
use App\Models\Service;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Location;
use App\Models\PublicSlot;
use App\Models\Appointment;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\SettingPublicBooking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\V1\ServiceResource;
use App\Http\Resources\V1\CategoryResource;
use App\Http\Resources\V1\CustomerResource;
use App\Http\Resources\V1\LocationResource;
use App\Http\Resources\V1\PublicSlotResource;
use App\Http\Resources\V1\PublicStaffResource;
use App\Http\Resources\V1\SettingPublicBookingResource;
use App\Models\OnlineHour;

class PublicSlotController extends Controller
{
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'organisations_id' => 'required|exists:organisations,id',
            'staff_id' => 'required|exists:staff,id',
            'service_id' => 'required|exists:services,id',
            'categories_id' => 'required|exists:categories,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'accept_privacy_terms' => 'required|boolean',
            'accept_terms_conditions' => 'required|boolean',
            'subscribe_newsletter' => 'nullable|boolean',
            'email' => 'required|email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);
        $customer_data = $request->all();
        $additional_data = [
            'birth_date' => Carbon::create(2000, 1, 1),
            'gender' => "not_specified",
        ];

        $customer = Customer::where('email', $customer_data['email'])->first();

        if (!$customer) {
            $customer = Customer::create([
                'email' => $customer_data['email'],
                'first_name' => $customer_data['first_name'],
                'last_name' => $customer_data['last_name'],
                'phone_number' => $customer_data['phone_number'],
                'gender' => 'not_specified',
                'country_id' => 52,
                'organisation_id' => $customer_data['organisations_id'],
                'birth_date' => Carbon::create(2000, 1, 1),
            ]);
        }

        DB::beginTransaction();
        try {
            $public_slot_is_exist = PublicSlot::where('organisations_id', $request->organisations_id)
                ->where('date', $request->date)
                ->where('staff_id', $request->staff_id)
                ->where(
                    fn($query) =>
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                        ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                        ->orWhere(
                            fn($subQuery) =>
                            $subQuery->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time)
                        )
                )->lockForUpdate()->exists();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Slot is already booked'], 404);
        }


        if ($public_slot_is_exist) {
            return response()->json(['message' => 'Slot is already booked'], 404);
        }

        $public_slot = PublicSlot::create([
            'email' => $customer->email,
            'first_name' => $customer->first_name ?? $request->first_name,
            'last_name' => $customer->last_name ?? $request->last_name,
            'phone' => $customer->phone_number ?? $customer_data['phone_number'],
            'customer_id' => $customer->id,
            'organisations_id' => $request->organisations_id,
            'staff_id' => $request->staff_id,
            'service_id' => $request->service_id,
            'categories_id' => $request->categories_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'accept_privacy_terms' => $request->accept_privacy_terms,
            'accept_terms_conditions' => $request->accept_terms_conditions,
            'subscribe_newsletter' => $request->subscribe_newsletter,
            'status' => '1',
        ]);

        $appointment = Appointment::withoutEvents(function () use ($public_slot, $request, $customer) {
            return Appointment::create(
                [
                    'customer_id' => $customer->id,
                    'service_id' => $public_slot->service->id,
                    'date' => $request->date,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'organisation_id' => $request->organisations_id,
                    'staff_id' => $request->staff_id,
                    'status' => null,
                    'price' => $public_slot->service->price,
                    'duration' => $public_slot->service->duration,
                    'location_id' => $request->organisations_id,
                    'is_online' => 1,
                ]
            );
        });

        if($customer->vip) {
            $activeOffers = $customer->vip->computeDiscountedPrice($public_slot->service->price, false);
            $appointment->update(['price' => $activeOffers]);
            $appointment->sendEmailNotification('customer_booking_vip');
        }

        DB::commit();

        return response()->json([
            'message' => 'Booking Successfully Created',
            'status' => 201,
            'public_slot' => new PublicSlotResource($public_slot),
            'customer' => new CustomerResource($customer),
            'appointment' => $appointment,
        ], 201);
    }

    public function show(Request $request, string $slug)
    {
        $params = $request->only(['location_id', 'category_id', 'service_id', 'staff_id', 'current_time']);
        if (!array_key_exists('current_time', $params) || empty($params['current_time'])) {
            $params['current_time'] = Carbon::now()->format('Y-m-d H:i:s');
        }
        $timeParts = explode(' ', $params['current_time']);
        if (!isset($timeParts[1])) {
            return response()->json([
                'message' => 'Format current_time tidak valid'
            ], 400);
        }

        $currentTime = $timeParts[1];
        $currentTime = explode(' ', $params['current_time'])[1];
        $organisation = Organisation::with('locations')
            ->where('slug', $slug)
            ->firstOrFail();

        $response = [
            'organisation' => [
                'id'   => $organisation->id,
                'name' => $organisation->name,
            ]
        ];

        if (empty($params['location_id'])) {
            $response['locations'] = LocationResource::collection($organisation->locations);
            return response()->json([
                'message' => 'Please select location',
                'status'  => 200,
                'data'    => $response
            ]);
        }

        $location = Location::where('id', $params['location_id'])
            ->where('organisation_id', $organisation->id)
            ->firstOrFail();
        $response['organisation']['location'] = new LocationResource($location);

        if (empty($params['category_id'])) {
            $categories = Category::whereHas('services', function ($query) use ($params, $organisation) {
                $query->whereHas('staff', function ($subQuery) use ($params, $organisation) {
                    $subQuery->whereHas('locations', function ($q) use ($params) {
                        $q->where('locations.id', $params['location_id']);
                    })->where('default_organisation_id', $organisation->id);
                })->where('organisation_id', $organisation->id);
            })->get();

            $response['categories'] = CategoryResource::collection($categories);
            return response()->json([
                'message' => 'Please select category',
                'status'  => 200,
                'data'    => $response
            ]);
        }
        $category = Category::where('id', $params['category_id'])->firstOrFail();
        $response['organisation']['category'] = new CategoryResource($category);

        if (empty($params['service_id'])) {
            $services = Service::where('category_id', $params['category_id'])
            ->where('organisation_id', $organisation->id)
            ->whereHas('staff', function ($q) use ($params, $organisation) {
                $q->whereHas('locations', function ($q2) use ($params) {
                    $q2->where('locations.id', $params['location_id']);
                })
                    ->where('default_organisation_id', $organisation->id)
                    ->where('service_staff.is_online', 1);
            })->get();

            $response['services'] = ServiceResource::collection($services);
            return response()->json([
                'message' => 'Please select service',
                'status'  => 200,
                'data'    => $response
            ]);
        }

        $service = Service::where('id', $params['service_id'])
            ->where('organisation_id', $organisation->id)
            ->firstOrFail();
        $response['organisation']['service'] = new ServiceResource($service);


        if (empty($params['staff_id'])) {
            $staff = Staff::whereHas('services', function ($q) use ($params) {
                $q->where('services.id', $params['service_id'])
                    ->where('service_staff.is_online', 1);
            })->get();

            $response['staff'] = PublicStaffResource::collection($staff);
            return response()->json([
                'message' => 'Please select staff',
                'status'  => 200,
                'data'    => $response
            ]);
        }

        $staffChosen = Staff::where('id', $params['staff_id'])->firstOrFail();
        $response['organisation']['staff'] = new PublicStaffResource($staffChosen);

        return response()->json([
            'message' => 'Data retrieved successfully',
            'status'  => 200,
            'data'    => $response
        ]);
    }

    public function getopen(string $slug)
    {
        $isOpen = SettingPublicBooking::with('media', 'organisation')->whereHas('organisation', fn($query) => $query->where('slug', $slug))->first();
        return response()->json(new SettingPublicBookingResource($isOpen));
    }

    public function selectDateTime(Request $request, Service $service)
    {
        $selectedDate       = $request->input('selected_date');
        $locationId         = $request->input('location_id');
        $staffId            = $request->input('staff_id');
        $clientCurrentTime  = $request->input('current_time');

        if (!$selectedDate || !$locationId) {
            return response()->json([
                'message' => 'selected_date and organisation_id are required'
            ], 400);
        }

        $now = $clientCurrentTime ? Carbon::parse($clientCurrentTime) : Carbon::now();

        $dayNumber = Carbon::parse($selectedDate)->format('N');
        $isToday   = Carbon::parse($selectedDate)->isToday();

        $defaultDuration = $service->duration;

        $getLocation = Location::findOrFail($locationId);

        $allHours = OnlineHour::where('location_id', $locationId)
            ->where('staff_id', $staffId)
            ->whereHas('services', function ($query) use ($service) {
                $query->where('services.id', $service->id);
            })
            ->get();

        $groupedHours = $allHours->groupBy('day');

        $days = range(1, 7);
        $activeDays = [];
        $inactiveDays = [];
        foreach ($days as $day) {
            if ($groupedHours->has($day)) {
                $hours = $groupedHours->get($day);

                if ($hours->contains(function ($hour) {
                    return (bool) $hour->is_available;
                })) {
                    $activeDays[] = $day;
                } else {
                    $inactiveDays[] = $day;
                }
            } else {

                $inactiveDays[] = $day;
            }
        }

        $selectedHours = $allHours->filter(function ($hour) use ($dayNumber) {
            return $hour->day == $dayNumber && $hour->is_available;
        });

        $slots = [];
        if ($selectedHours->isNotEmpty()) {
            foreach ($selectedHours as $hour) {
                $staffStart = Carbon::parse($selectedDate . ' ' . $hour->start_time);
                $staffEnd   = Carbon::parse($selectedDate . ' ' . $hour->end_time);

                $locationStart = Carbon::parse($selectedDate . ' ' . $getLocation->from_time);
                $locationEnd   = Carbon::parse($selectedDate . ' ' . $getLocation->to_time);

                $adjustedStart = $staffStart->lt($locationStart) ? $locationStart : $staffStart;
                $adjustedEnd   = $staffEnd->gt($locationEnd) ? $locationEnd : $staffEnd;

                if ($isToday && $adjustedStart->lt($now)) {
                    $adjustedStart = $now->copy()->addMinute()->startOfMinute();
                }

                if ($adjustedStart->gte($adjustedEnd)) {
                    continue;
                }

                $currentSlotStart = $adjustedStart->copy();
                while ($currentSlotStart->lt($adjustedEnd)) {
                    $slotStart = $currentSlotStart->format('H:i');
                    $nextSlot  = $currentSlotStart->copy()->addMinutes($defaultDuration);
                    if ($nextSlot->gt($adjustedEnd)) {
                        $nextSlot = $adjustedEnd;
                    }
                    $slotEnd = $nextSlot->format('H:i');

                    $slots[] = $slotStart . ' - ' . $slotEnd;

                    $currentSlotStart->addMinutes($defaultDuration);
                }
            }
        }

        $slots = array_values(array_unique($slots));
        sort($slots);

        return response()->json([
            'selected_date' => $selectedDate,
            'duration'      => $defaultDuration,
            'day'           => $dayNumber,
            'slots'         => $slots,
            'active'        => $activeDays,
            'in_active'     => $inactiveDays 
        ]);
    }
}
