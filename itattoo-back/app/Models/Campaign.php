<?php

namespace App\Models;

use Carbon\Carbon;
use App\Mail\CampaignEmail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Gate;

class Campaign extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'filters' => 'array',
        'delievered_on' => 'datetime',
        'is_active' => 'boolean',
        'is_birthday' => 'boolean',
    ];
    // when birthday of a customer that booked in the last X months
    // when user didn't have any appointments in the past X months
    // holidays

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

    protected function scheduledTime(): Attribute
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

    public function isDue():bool
    {
        $campaignDate = Carbon::parse($this->scheduled_date . ' ' . $this->scheduled_time, $this->timezone);

        return $campaignDate->isPast();
    }

    /**
     * Sends an email or SMS to all customers included in the campaign
     *
     * @return void
     */
    public function deliver()
    {
        if (! $this->is_active) {
            return;
        }

        logger('Checking campaign: ' . $this->name);

        $filters = $this->filters;
        $date = now()->subMonths($filters['past_months']);
        $campaignHour = Carbon::parse($this->scheduled_time, $this->timezone)->hour;

        if ($this->is_birthday && $campaignHour != now($this->timezone)->hour) {
            return;
        }
        logger('Campaign is ready to deliver: ' . $this->name);

        if ((bool) $filters['have_bookings']) {
            $customers = $this->organisation->customers()
                        ->with('organisation')
                        ->when($this->is_birthday, function ($query) {
                            $query->whereDay('birth_date', now());
                        })->whereHas('appointments', function ($q) use ($date) {
                            return $q->withoutGlobalScopes()->whereDate('date', '>', $date);
                        })->get();
        } else {
            $customers = $this->organisation->customers()
                        ->with('organisation')
                        ->when($this->is_birthday, function ($query) {
                            $query->whereDay('birth_date', now());
                        })->whereDoesntHave('appointments', function ($q) use ($date) {
                            return $q->withoutGlobalScopes()->whereDate('date', '>', $date);
                        })->get();
        }

        if ($this->type == 'email') {
            foreach ($customers as $k => $customer) {
                if (! $customer->accepts_newsletter) {
                    continue;
                }

                if(Gate::denies('sendMarket', $this->organisation)) {
                    logger('Email not allowed for this user');
                    return false;
                }
                
                Mail::mailer('postmarkmarketing')->to($customer->email)->queue(new CampaignEmail($this, $customer));
            }
        } else {
            foreach ($customers as $customer) {
                if (! $customer->accepts_newsletter) {
                    continue;
                }

                if(Gate::denies('sendSms', $this->organisation)) {
                    logger('SMS not allowed for this user');
                    return false;
                }
                
                Sms::sendNewsletter($customer, $this->message);
            }
        }

        if (! $this->is_birthday) {
            $this->update(['delivered_on' => now()]);
        }
    }
}
