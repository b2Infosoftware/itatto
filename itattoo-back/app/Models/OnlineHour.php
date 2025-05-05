<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineHour extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function services()
    {
        return $this->belongsToMany(Service::class, 'online_hour_service', 'online_hour_id', 'service_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
