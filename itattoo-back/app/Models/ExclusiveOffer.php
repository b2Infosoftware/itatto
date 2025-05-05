<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExclusiveOffer extends Model
{
    use HasFactory;

    protected $table = 'exclusive_offers';

    protected $fillable = [
        'offer_name',
        'offer_details',
        'start_date',
        'end_date',
        'location_id'
    ];

    protected $with = ['discount', 'vips'];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function vips()
    {
        return $this->belongsToMany(Vip::class, 'exclusive_offers_vips', 'exclusive_offer_id', 'vip_id');
    }


    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function discount(): HasOne
    {
        return $this->hasOne(Discount::class);
    }
}
