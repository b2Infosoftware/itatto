<?php

namespace App\Http\Controllers\V1;

use App\Models\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\LogResource;

class LogController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->hasAccessTo('view', 'logs')) {
            abort(403);
        }

        $logs = Log::whereOrganisationId(auth()->user()->default_organisation_id)->orderByDesc('id')->paginate(15);

        return LogResource::collection($logs);
    }
}
