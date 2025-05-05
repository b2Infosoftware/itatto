<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarSettings extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'hidden_days' => 'array',
        'allow_off_hours_booking' => 'boolean',
        'allow_double_booking' => 'boolean',
        'apply_staff_appearance' => 'boolean',
        'use_staff_colors' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */
    public function organisation() : BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS AND MUTATORS
    |--------------------------------------------------------------------------
    */
    public function getFromTimeAttribute($value)
    {
        return Str::replaceLast(':00', '', $value);
    }

    public function getToTimeAttribute($value)
    {
        return Str::replaceLast(':00', '', $value);
    }

    public function getStartTimeAttribute($value)
    {
        return Str::replaceLast(':00', '', $value);
    }
}
