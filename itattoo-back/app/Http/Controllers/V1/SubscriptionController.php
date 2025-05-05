<?php

namespace App\Http\Controllers\V1;

use App\Models\Subscription;
use App\Http\Controllers\Controller;

class SubscriptionController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function index()
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        $subscriptions = Subscription::with('organisation.owner', 'plan')->active()->get();

        return response()->json(['data' => $subscriptions]);
    }
}
