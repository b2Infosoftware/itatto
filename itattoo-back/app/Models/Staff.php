<?php

namespace App\Models;

use services;
use App\Mail\ResetPassword;
use App\Traits\HasPermissions;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Staff extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasPermissions;

    protected $with = ['tags', 'services', 'availability', 'timeOff'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'staff';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'staff_shortlist_ids' => 'array',
        'view_statistics' => 'boolean',
        'is_guest' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS AND MUTATORS
    |--------------------------------------------------------------------------
    */

    /**
     * Mutator for hashing the password on save
     *
     * @param string $value
     * @return void
     */
    public function password(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Hash::make($value),
        );
    }

    /**
     * Mutator for getting the full name attribute of a staff member
     *
     * @return Attribute
     */
    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) =>  $attributes['first_name'] . ' ' . $attributes['last_name'],
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */
    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(Organisation::class)->withPivot(['confirmed_at', 'role_id']);
    }

    public function organisationOwner(): HasOne
    {
        return $this->hasOne(Organisation::class, 'owner_id');
    }

    public function defaultOrganisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'default_organisation_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)->withPivot('is_online');
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class);
    }

    public function defaultLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'default_location_id');
    }

    public function availability(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function timeOff(): HasMany
    {
        return $this->hasMany(TimeOff::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id', 'role_id');
    }

    public function completedAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->whereDate('date', '<', now());
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class);
    }

    public function onlineHours()
    {
        return $this->hasMany(OnlineHour::class);
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Sends the user an email with a link to reset its password
     *
     * @param string $token
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = config('app.client') . '/reset-password?token=' . $token . '&email=' . $this->email;
        Mail::to($this->email)->queue(new ResetPassword($this, $url));
    }

    /**
     * Check if email has been verified.
     *
     * @return bool
     */
    public function isActive()
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Generates set of availability models for its schedule
     *
     * @return void
     */
    public function generateAvailability()
    {
        $locations = $this->locations()->get();
        Availability::whereStaffId($this->id)->whereNotIn('location_id', $locations->pluck('id')->all())->delete();
        foreach ($locations as $location) {
            if (Availability::whereStaffId($this->id)->whereLocationId($location->id)->exists()) {
                continue;
            }
            foreach (range(1, 7) as $day) {
                Availability::create([
                    'staff_id' => $this->id,
                    'organisation_id' => $location->organisation_id,
                    'location_id' => $location->id,
                    'is_available' => true,
                    'start_time' => '10:00:00',
                    'end_time' => '20:00:00',
                    'day' => $day,
                ]);
            }
        }
    }

    /**
     * Checkes whether a user has super-admin privilleges.
     * Better to have it this way than in db for security.
     *
     * @return bool
     */
    public function isSuperAdmin() : bool
    {
        return $this->id == 1;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeInLocation($query, $locationId) : Builder
    {
        if (! $locationId) {
            return  $query->whereHas('locations', function ($q) {
                return $q->where('location_id', auth()->user()->default_location_id);
            });
        }

        return $query->whereHas('locations', function ($q) use ($locationId) {
            return $q->where('location_id', $locationId);
        });
    }
}
