<?php

namespace App\Http\Controllers\V1;

use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    /**
     * TODO: Cache the settings
     * Display the specified resource.
     */
    public function index()
    {
        $data['countries'] = Country::get(['name', 'id']);
        $data['currencies'] = Currency::all();
        $data['dynamicVariables'] = config('customVariables.mail');
        $data['languages'] = Language::all();

        return response()->json($data);
    }
}
