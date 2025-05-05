<?php

namespace App\Http\Controllers\V1;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\StripeService;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PlanResource;
use Illuminate\Support\Facades\Log;

class PlanController extends Controller
{
    protected $stripeService;

    public function __construct()
    {
        $this->stripeService = new StripeService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $organisation = auth()->user()->defaultOrganisation;
        if ($organisation->trial_ends_at && Carbon::parse($organisation->trial_ends_at)->lt(now())) { 
            return PlanResource::collection(Plan::where('price', '>', 0)->get());
        }
        return PlanResource::collection(Plan::all());
    }

    /**
     * Subscribes a logged-in user to a plan.
     * Can also be called to upgrade or downgrade an existing subscription plan
     *
     * @param \App\Models\Plan $plan
     * @return \Illuminate\Http\Response
     */
    public function subscribe(Plan $plan)
    {
        $organisation = auth()->user()->defaultOrganisation;
        $subscription = $organisation->activeSubscription;

        if($plan->months == 0) {
            $session = $this->stripeService->createCheckoutSessionFreePlan($plan);
            return response()->json(['url' => $session->url]);
        }

        if (! $subscription || empty($subscription->stripe_id)) {
            $session = $this->stripeService->createCheckoutSession($plan);
            return response()->json(['url' => $session->url]);
        }

        if ($subscription->plan_id === $plan->id) {
            return response()->json(['message' => 'Sense makes none -_- Stop trying this.'], 403);
        }

        if (!is_null($organisation->trial_ends_at)) {
            if ($subscription->wasCanceled()) {
                return response()->json(['message' => 'You can\'t change a cancelled subscription'], 403);
            }

            $session = $this->stripeService->createCheckoutSession($plan);
            return response()->json(['url' => $session->url]);
        }

        $g = $this->stripeService->changeSubscriptionPlan($subscription, $plan);

        return response()->json(['data' => $g]);
    }

    public function trial(Plan $plan)
    {
        $g = $this->stripeService->createCheckoutSessionFreePlan($plan);
        
        return response()->json([
            'url' => $g->url,
            'data' => $g
        ]);
    }

    public function indexTrial()
    {
        $user = auth()->user();
        $organisation = $user->defaultOrganisation;

        $existingTrial = $organisation->activeSubscription()
            ->where('is_trial', true)
            ->whereNotNull('trial_ends_at')
            ->exists();

        $hasPaidSubscription = $organisation->activeSubscription()
            ->where('is_trial', false)
            ->whereNotNull('ends_at')
            ->exists();

        if ($existingTrial || $hasPaidSubscription) {
            return response()->json(['message' => 'Trial not available.'], 403);
        }

        $trialPlan = Plan::where('months', 0)->first();
        return new PlanResource($trialPlan);
    }

    /**
     * Subscribes a logged-in user to a plan.
     * Can also be called to upgrade or downgrade an existing subscription plan
     *
     * @param \App\Models\Plan $plan
     * @return \Illuminate\Http\Response
     */
    public function cancel(Plan $plan)
    {
        $subscription = auth()->user()->defaultOrganisation->activeSubscription;

        if (! $subscription || $subscription->plan_id !== $plan->id) {
            return response()->json(['message' => 'You don\'t have any active subscriptions for this plan.'], 403);
        }

        try {
            $this->stripeService->cancelSubscription($subscription);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'There was an error. Please contact us.'], 403);
        }

        return response()->json(['message' => 'The subscription has been canceled'], 200);
    }

    /**
     * Attempts to purchase sms
     *
     * @param \Illuminate\Http\Request $plan
     * @return \Illuminate\Http\Response
     */
    public function buySMS(Request $request)
    {
        $subscription = auth()->user()->defaultOrganisation->activeSubscription;

        if (! $subscription) {
            return response()->json(['message' => 'You cannot buy SMS without a subscriptions.'], 403);
        }

        $planId = config('sms.plans.' . $request->amount);

        if (! $planId) {
            return abort(404);
        }

        try {
            return  $this->stripeService->createCheckoutSessionForSms($planId);
        } catch (\Throwable $th) {


            return response()->json(['message' => 'There was an error. Please contact us.'], 403);
        }
    }

    public function checkTrial()
    {
        $user = auth()->user();
        $organisation = $user->defaultOrganisation;
        $indexTrialTime = $organisation->trial_ends_at >= date(now());

        $getSubscription = Subscription::withoutGlobalScopes()->where('organisation_id', $organisation->id)->whereNot('is_trial', 1)->get()->last();
        $getSubscriptionTrial = Subscription::withoutGlobalScopes()->where('organisation_id', $organisation->id)->where('is_trial', 1)->get();

        $indexOwnedTrial = $getSubscriptionTrial->count() > 0;
        $indexOwnedPlan = !is_null($getSubscription);

        $indexLastSubscription = optional($getSubscription)->ends_at ? $getSubscription->ends_at->toDateTimeString() >= now()->toDateTimeString() : false;

        if($indexTrialTime === false && $indexLastSubscription === false) {
            $status = 0;
        }

        if($indexTrialTime === true && $indexOwnedTrial === true) {
            $status = 0;
        }

        if($indexOwnedTrial === false){
            $status = 0;
        }

        if($indexOwnedPlan === true && $indexOwnedTrial === false){
            $status = 0;
        }

        if($indexOwnedPlan === true && $indexOwnedTrial === true){
            $status = 1;
        }

        return response()->json([
            'status' => $status ?? 1
        ]);
    }
}
