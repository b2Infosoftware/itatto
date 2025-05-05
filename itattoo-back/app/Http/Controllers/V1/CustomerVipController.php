<?php

namespace App\Http\Controllers\V1;

use App\Models\Vip;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CustomerVipController extends Controller
{
    public function setvip(){
        $user = Auth::user()->id;
        $location_id = Location::where('user_id', $user)->first();
        dd($location_id);
        
        $vip = new Vip();
        $vip->label = 'VIP';
        $vip->location_id = 1;
        $vip->save();
        return response()->json($vip);
    }
}
