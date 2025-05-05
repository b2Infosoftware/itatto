<?php

namespace App\Http\Controllers\V1;

use App\Models\Log;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\CustomersExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\V1\CustomerRequest;
use App\Http\Resources\V1\CustomerResource;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Customer $customer
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customers = auth()->user()->defaultOrganisation->customers()->with('vip')->filtered()->paginate(6);

        return CustomerResource::collection($customers);
    }

    /**
     * Show the values for the given resource.
     *
     * @param Customer $customer
     * @return \App\Http\Resources\CustomerResource
     */
    public function show(Customer $customer)
    {
        $this->authorize('view', $customer);

        $customer->load('appointments', 'projects', 'media', 'vip');

        return new CustomerResource($customer);
    }

    /**
     * Store a new entry in the database.
     *
     * @param Customer $customer
     * @return \App\Http\Resources\CustomerResource
     */
    public function store(CustomerRequest $request)
    {
        $this->authorize('create', Customer::class);

        if ($request->has('parent_1')) {
            $parent_1 = Customer::create($request->parent_1);
            $request->merge(['parent_1_id'=> $parent_1->id]);
        }
        if ($request->has('parent_2')) {
            $parent_2 = Customer::create($request->parent_2);
            $request->merge(['parent_2_id'=> $parent_2->id]);
        }
        $customer = Customer::create($request->except(['parent_1', 'parent_2']));
        Log::addLog('client_created', $customer);

        $customer->sendNewsletterApprovalConfirmation();

        return response()->json([
            'message' => trans('general.create', ['resource' => trans('resource.customer')]),
            'data' => new CustomerResource($customer),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->updateFromRequest($request);

        if ($request->staff_ids) {
            $customer->staff()->sync($request->staff_ids);
        }

        Log::addLog('client_updated', $customer);

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.customer')]),
            'data' => new CustomerResource($customer),
        ], 200);
    }

    /**
     * Remove the specified resource from database.
     *
     *
     * @param Customer $customer
     *
     * @return \App\Http\Resources\ResponseResource
     */
    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);

        try {
            $customer->organisations()->detach(auth()->user()->default_organisation_id);
            if ($customer->organisations()->count() === 0) {
                $customer->projects()->delete();
                $customer->delete();
            }

            return response()->json(['message' => trans('general.delete', ['resource' => trans('resource.customer')])]);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['message' => trans('general.error')], 500);
        }
    }

    /**
     * Gets the stats of a customer
     *
     * @return \Illuminate\Http\Response
     */
    public function getStats(Customer $customer)
    {
        $this->authorize('viewStats', $customer);
        $groupBy = request()->get('month') == 'all' ? 'm' : 'd';
        $year = request()->has('year') ? request()->get('year') : date('Y');

        $interval[] = Carbon::parse($year . '-02-21')->startOfYear();
        $interval[] = Carbon::parse($year . '-02-21')->endOfYear();
        $data = $customer->appointments()->with('service')->whereBetween('date', $interval)->orderBy('date')->get();
        $res['byService'] = $data->groupBy('service.name')->map(function ($item, $key) use ($data) {
            return [
                'name' => $key,
                'percent' => number_format($item->count() / $data->count() * 100, 2),
            ];
        })->values();

        $appointments = $data->groupBy(function ($item) use ($groupBy) {
            return (int) \Carbon\Carbon::parse($item->date)->format($groupBy);
        })->map(function ($item) {
            return $item->count();
        });

        $res['byDate'] = [];
        // prefill all empty spots
        if (request()->get('month') == 'all') {
            // actually is months
            $days = 12;
        } else {
            $days = now()->setYear($year)->setMonth(request()->get('month'))->setDay(15)->daysInMonth;
        }
        for ($i = 1; $i <= $days; $i++) {
            $res['byDate'][$i] = $appointments->has($i) ? $appointments[$i] : 0;
        }

        return response()->json(['data' => $res]);
    }

    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportCustomers(Request $request)
    {
        $fileName = 'customers_export_' . time() . '.xlsx';

        $path = 'exports/' . $fileName;

        Excel::store(new CustomersExport, $path, 'public');
        $url = asset(Storage::url($path));

        return response()->json(compact('url'), 201);
    }

    public function acceptNewsletter(Customer $customer, Request $request)
    {
        if (! $request->hasValidSignature()) {
            return abort(422, 'Something went wrong');
        }

        $customer->update([
            'accepts_newsletter' => true,
            'accepted_newsletter_at' => now(),
        ]);

        return response()->json(['message' => 'Updated']);
    }

    public function rejectNewsletter(Customer $customer, Request $request)
    {
        if (! $request->hasValidSignature()) {
            return abort(422, 'Something went wrong');
        }

        $customer->update([
            'accepts_newsletter' => false,
            'declined_newsletter_at' => now(),
        ]);

        return response()->json(['message' => 'Updated']);
    }
}
