<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Services\StripeService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organisation extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($organisation) {
            $slug = Str::slug($organisation->name);
            $existingSlug = Organisation::where('slug', $slug)->first();
            if ($existingSlug) {
                $randomNumber = mt_rand(1000000, 9999999);
                $slug = "{$slug}-{$randomNumber}";
            }
            $organisation->slug = $slug;
        });
    }

    protected $with = ['calendarSettings', 'currency'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organisations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hidden_fields' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */
    public function media()
    {
        return $this->hasMany(Media::class, 'organisation_id');
    }

    public function settingPublicBooking()
    {
        return $this->hasOne(SettingPublicBooking::class, 'organisation_id');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class)->withPivot('confirmed_at');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function calendarSettings(): HasOne
    {
        return $this->hasOne(CalendarSettings::class);
    }

    public function notificationSettings(): HasOne
    {
        return $this->hasOne(NotificationSettings::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->ofMany(['ends_at' => 'max'], function ($query) {
                $query->where('ends_at', '>', now());
            });
    }

    /**
     * Returns the stripe_id of the user.
     * If the field is empty, it creates a stripe customer
     * and associates the newly created customer with the current user
     *
     * @return string
     */
    public function getStripeId()
    {
        if (! $this->stripe_id) {
            $stripeService = new StripeService;
            $customer = $stripeService->createCustomer($this);
            $this->update(['stripe_id' => $customer->id]);
        }

        return $this->stripe_id;
    }

    /**
     * Decides whether an organisation can send sms
     */
    public function cannotSendSms()
    {
        return $this->sms_left < 1 && config('app.env') == 'production';
    }

    public function decrementSms()
    {
        if ($this->sms_left > 0) {
            $this->decrement('sms_left');
        }
    }
}
