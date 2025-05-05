<?php

namespace App\Http\Controllers\V1;

use App\Models\Campaign;
use App\Models\Customer;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CampaignRequest;
use App\Http\Resources\V1\CampaignResource;
use App\Models\Plan;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function isTrial()
    {
        $user = auth()->user();
        $organisation = $user->defaultOrganisation;
        $trialPlan = Plan::where('price', 0.00)
            ->where('months', 0)
            ->first();
        $trialSubscription = null;
        if ($trialPlan) {
            $trialSubscription = Subscription::where('organisation_id', $organisation->id)
                ->where('plan_id', $trialPlan->id)
                ->orderByDesc('id')
                ->first();
        }
        $activeTrial = $trialSubscription && $trialSubscription->ends_at
            ? now()->lessThanOrEqualTo($trialSubscription->ends_at)
            : false;
        $activeOtherPlan = Subscription::where('organisation_id', $organisation->id)
            ->when($trialPlan, function ($query) use ($trialPlan) {
                return $query->where('plan_id', '!=', $trialPlan->id);
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->exists();
        if ($activeTrial) {
            return true; 
        } elseif (!$activeTrial && $activeOtherPlan) {
            return false; 
        } else {
            return true;
        }
    }

    public function index()
    {
        if($this->isTrial() === false) {
            $campaigns = Campaign::whereOrganisationId(auth()->user()->default_organisation_id)->orderByDesc('id')->get();
            return CampaignResource::collection($campaigns);
        } else {
            return response()->json(['message' => 'Data Not Found'], 404);
        }
    }

    /**
     * Shows the values of the given resource
     *
     * @param \App\Models\Campaign $campaign
     * @return \App\Http\Resources\CampaignResource
     */
    public function show(Campaign $campaign)
    {
        if($this->isTrial() === false) {
            return response()->json([
                'message' => trans('general.create', ['resource' => trans('resource.campaign')]),
                'data' => new CampaignResource($campaign),
            ], 200);
        } else {
            return response()->json(['message' => 'Data Not Found'], 404);
        }
    }

    /**
     * Store a new entry in the database.
     *
     * @param \App\Models\Campaign $campaign
     * @return \App\Http\Resources\CampaignResource
     */
    public function store(CampaignRequest $request)
    {
        if($this->isTrial() === false) {
            $campaign = Campaign::create($request->except('is_birthday'));
            return response()->json([
                'message' => trans('general.create', ['resource' => trans('resource.campaign')]),
                'data' => new CampaignResource($campaign),
            ], 200);
        } else {
            return response()->json(['message' => 'Data Not Found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CampaignRequest  $request
     * @param  \App\Models\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function update(CampaignRequest $request, Campaign $campaign)
    {
        if($this->isTrial() === false) {
            $campaign->update($request->except(['delivered_on', 'is_birthday']));
            return response()->json([
                'message' => trans('general.update', ['resource' => trans('resource.campaign')]),
                'data' => new CampaignResource($campaign),
            ], 200);
        } else {
            return response()->json(['message' => 'Data Not Found'], 404);
        }
    }

    /**
     * Remove the specified resource from database.
     *
     *
     * @param \App\Models\Campaign $campaign
     *
     * @return \App\Http\Resources\ResponseResource
     */
    public function destroy(Campaign $campaign)
    {
        if($this->isTrial() === false) {
            try {
                $campaign->delete();
                return response()->json(['message' => trans('general.delete', ['resource' => trans('resource.campaign')])]);
            } catch (\Exception $e) {
                report($e);

                return response()->json(['message' => trans('general.error')], 500);
            }
        } else {
            return response()->json(['message' => 'Data Not Found'], 404);
        }
    }

    /**
     * Counts the potential customers.
     *
     * @return \Illuminate\Http\Response
     */
    public function countCustomers(Request $request)
    {
        if($this->isTrial() === false) {
            $date = now()->subMonths($request->past_months);
            $count = 0;
            $orgId = auth()->user()->default_organisation_id;
            if ((bool) $request->have_bookings) {
                $count = Customer::whereOrganisationId($orgId)->where('accepts_newsletter', 1)->whereHas('appointments', function ($q) use ($date) {
                    return $q->whereDate('date', '>', $date);
                })->count();
            } else {
                $count = Customer::whereOrganisationId($orgId)->where('accepts_newsletter', 1)->whereDoesntHave('appointments', function ($q) use ($date) {
                    return $q->whereDate('date', '>', $date);
                })->count();
            }
            return response()->json(['data' => $count]);
        } else {
            return response()->json(['message' => 'Data Not Found'], 404);
        }
    }

    /**
     * Previews an email
     *
     * @return \Illuminate\Http\Response
     */
    public function preview(CampaignRequest $request)
    {
        if($this->isTrial() === false) {
            $companyName = auth()->user()->defaultOrganisation->name;
            $tmpCampaign = new Campaign();
            $tmpCampaign->message = $request->message;
            $view = view('emails.campaign')->with([
                'companyName' => $companyName,
                'customerName' => 'John Doe',
                'campaign' => $tmpCampaign,
            ])->render();

            return response()->json(['data' => $view]);
        } else {
            return response()->json(['message' => 'Data Not Found'], 404);
        }
    }
}
