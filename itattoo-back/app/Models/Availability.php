<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Availability extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'availability';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_available' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS AND MUTATORS
    |--------------------------------------------------------------------------
    */
    protected function startTime(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Str::replaceLast(':00', '', $value),
        );
    }

    protected function endTime(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Str::replaceLast(':00', '', $value),
        );
    }
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    // public function staffAvailability($query)
    // {
    //     return $query->wh
    // }
}
