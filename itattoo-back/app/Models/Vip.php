<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vip extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'location_id',
        'color'
    ];

    protected $guarded = ['id'];

    protected $with = [];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function exclusiveOffers(): BelongsToMany
    {
        return $this->belongsToMany(ExclusiveOffer::class, 'exclusive_offers_vips', 'vip_id', 'exclusive_offer_id');
    }

    public function activeExclusiveOffers()
    {
        return $this->belongsToMany(ExclusiveOffer::class, 'exclusive_offers_vips', 'vip_id', 'exclusive_offer_id')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with('discount');
    }

    /**
     * Calculate the price after discount.
     *
     * @param float $originalPrice Starting price.
     * @param bool  $useMultiDiscount If true, then apply multi discount (future feature).
     *
     * @return float Final price after discount.
     */
    public function computeDiscountedPrice($originalPrice, $useMultiDiscount = false)
    {
        $activeOffers = $this->activeExclusiveOffers()->get();

        $percentageOffers = $activeOffers->filter(function ($offer) {
            return $offer->discount && $offer->discount->discount_type == 1; // presentate
        });
        $fixedOffers = $activeOffers->filter(function ($offer) {
            return $offer->discount && $offer->discount->discount_type == 0; // fixed
        });

        if ($useMultiDiscount) {
            $finalPrice = $originalPrice;
            foreach ($activeOffers as $offer) {
                if ($offer->discount) {
                    if ($offer->discount->discount_type == 1) {
                        $finalPrice -= $finalPrice * ($offer->discount->discount_value / 100);
                    } elseif ($offer->discount->discount_type == 0) {
                        $finalPrice -= $offer->discount->discount_value;
                    }
                }
            }
            return $finalPrice;
        } else {
            if ($percentageOffers->isNotEmpty() && $fixedOffers->isNotEmpty()) {
                $percentageDiscount = $percentageOffers->first()->discount->discount_value;
                $fixedDiscount = $fixedOffers->first()->discount->discount_value;

                $priceAfterPercentage = $originalPrice - ($originalPrice * ($percentageDiscount / 100));
                $finalPrice = $priceAfterPercentage - $fixedDiscount;
                return $finalPrice;
            } elseif ($percentageOffers->isNotEmpty()) {
                $percentageDiscount = $percentageOffers->first()->discount->discount_value;
                return $originalPrice - ($originalPrice * ($percentageDiscount / 100));
            } elseif ($fixedOffers->isNotEmpty()) {
                $fixedDiscount = $fixedOffers->first()->discount->discount_value;
                return $originalPrice - $fixedDiscount;
            }
        }
        return $originalPrice;
    }
}
