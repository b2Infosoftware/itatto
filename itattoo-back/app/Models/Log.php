<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory;

    protected $connection = 'logs';

    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public static function addLog($action, $data, $userType = null)
    {
        if (auth()->user() && auth()->user()->isSuperAdmin()) {
            return;
        }

        if ($data instanceof Customer) {
            $staffName = null;
        } else {
            $staffName = $data->staff ? $data->staff->fullName : (($data instanceof Staff) ? $data->fullName : '');
        }

        $data = [
            'type' => $action,
            'service_name' => $data->service ? $data->service->name : '',
            'client_name' => ($data->customer) ? $data->customer->fullName : (($data instanceof Customer) ? $data->fullName : ''),
            'staff_name' => $staffName,
            'appointment_date' => $data->date ? $data->date : null,
            'start_time' => $data->start_time ? $data->start_time : null,
            'end_time' => $data->end_time ? $data->end_time : null,
            'by' => auth()->user() ? auth()->user()->fullName : $data->fullName || 'Guest',
            'organisation_id' => auth()->user() ? auth()->user()->default_organisation_id : (($data instanceof Staff) ? $data->default_organisation_id : $data->organisation_id),
            'ip' => request()->ip(),
        ];
        self::create($data);
    }
}
