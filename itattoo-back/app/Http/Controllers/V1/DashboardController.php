<?php

namespace App\Http\Controllers\V1;

use App\Models\Project;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function index()
    {
        if (! auth()->user()->hasAccessTo('view', 'dashboard')) {
            abort(403);
        }
        $organisation = auth()->user()->defaultOrganisation;
        $year = request()->get('year') ?? date('Y');
        $month = request()->get('month') ?? null;
        $locationId = request()->get('location_id') ?? null;
        $service_ids = request()->get('service_id') ? [request()->get('service_id')] : [];
        $groupBy = $month ? 'day' : 'month';

        if ($month) {
            $interval[] = Carbon::parse($year . '-' . $month . '-21')->startOfMonth();
            $interval[] = Carbon::parse($year . '-' . $month . '-21')->endOfMonth();
        } else {
            $interval[] = Carbon::parse($year . '-02-21')->startOfYear();
            $interval[] = Carbon::parse($year . '-02-21')->endOfYear();
        }

        $data['clients'] = $organisation->customers()->count();
        $data['upcoming_appointments'] = $organisation->appointments()->where(function ($q) use ($locationId) {
            if ($locationId == null) {
                return $q;
            }

            return $q->whereLocationId($locationId);
        })->withoutGlobalScopes()->upcoming()->forServiceIds($service_ids)->count();
        $data['total_appointments'] = $organisation->appointments()->where(function ($q) use ($locationId) {
            if ($locationId == null) {
                return $q;
            }

            return $q->whereLocationId($locationId);
        })->withoutGlobalScopes()->whereBetween('date', $interval)->forServiceIds($service_ids)->count();
        $data['total_income'] = $organisation->appointments()->where(function ($q) use ($locationId) {
            if ($locationId == null) {
                return $q;
            }

            return $q->whereLocationId($locationId);
        })->withoutGlobalScopes()->whereBetween('date', $interval)->forServiceIds($service_ids)->sum('price');

        $data['sms'] = $organisation->sms_left;

        $data['today_clients'] = $organisation->customers()->whereDate('created_at', '=', date('Y-m-d'))->count();
        $data['today_appointments'] = Appointment::whereDate('date', '=', date('Y-m-d'))->forServiceIds($service_ids)->count();
        $data['active_projects'] = Project::whereOrganisationId($organisation->id)->whereHas('appointments', function ($q) {
            $q->upcoming();
        })->count();
        $data['total_projects'] = Project::whereOrganisationId($organisation->id)->count();

        if ($month) {
            $appointments = $organisation->appointments()->withoutGlobalScopes()
                                        ->where(function ($q) use ($locationId) {
                                            if ($locationId == null) {
                                                return $q;
                                            }

                                            return $q->whereLocationId($locationId);
                                        })
                                        ->without('service', 'customer', 'project', 'staff', 'location')
                                        ->whereBetween('date', $interval)
                                        ->groupBy('day')
                                        ->forServiceIds($service_ids)
                                        ->select('date', DB::raw('DATE(date) day'), DB::raw('count(*) as total'))
                                        ->get()
                                        ->keyBy(function ($item) {
                                            return Carbon::parse($item->day)->format('j');
                                        })
                                        ->map(function ($item) {
                                            return $item['total'];
                                        });
        } else {
            $appointments = $organisation->appointments()
                                        ->withoutGlobalScopes()
                                        ->where(function ($q) use ($locationId) {
                                            if ($locationId == null) {
                                                return $q;
                                            }

                                            return $q->whereLocationId($locationId);
                                        })
                                        ->without('service', 'customer', 'project', 'staff', 'location')
                                        ->whereBetween('date', $interval)
                                        ->groupBy('month')
                                        ->forServiceIds($service_ids)
                                        ->select('date', DB::raw('MONTH(date) month'), DB::raw('count(*) as total'))
                                        ->get()
                                        ->keyBy('month')
                                        ->map(function ($item) {
                                            return  $item['total'];
                                        });
        }

        if ($month) {
            $days = now()->setYear($year)->setMonth(request()->get('month'))->setDay(15)->daysInMonth;
        } else {
            // actually is months
            $days = 12;
        }
        for ($i = 1; $i <= $days; $i++) {
            $data['by_date'][$i] = $appointments->has($i) ? $appointments[$i] : 0;
        }

        $data['by_service'] = Service::when(count($service_ids), function ($q) use ($service_ids) {
            $q->whereIn('id', $service_ids);
        })->whereOrganisationId($organisation->id)->with(['appointments' => function ($q) use ($interval, $locationId) {
            $q->withoutGlobalScopes()
            ->where(function ($q) use ($locationId) {
                if ($locationId == null) {
                    return $q;
                }

                return $q->whereLocationId($locationId);
            })
            ->whereBetween('date', $interval);
        }])->get()->map(function ($item) use ($locationId) {
            return[
                'name' => $item->name,
                'color' => $item->color,
                'appointments_count' => count($item->appointments),
            ];
        })->filter(function ($item) {
            return $item['appointments_count'] > 0;
        })->sortByDesc('appointments_count')->values();

        return response()->json(['data' => $data]);
    }
}
