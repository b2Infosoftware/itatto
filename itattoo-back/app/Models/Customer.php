<?php

namespace App\Models;

use App\Mail\ResetPassword;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewsletterApprovalEmail;
use Illuminate\Notifications\Notifiable;
use App\Http\Requests\V1\CustomerRequest;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $with = ['projects', 'staff'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customers';

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
        'is_minor' => 'boolean',
        'accepts_newsletter' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS AND MUTATORS
    |--------------------------------------------------------------------------
    */

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) =>  $attributes['first_name'] . ' ' . $attributes['last_name'],
        );
    }

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

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */
    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(Organisation::class);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function parent1(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_1_id');
    }

    public function parent2(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_2_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function unfinishedAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->upcoming()->incomplete();
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class);
    }

    public function vip(): BelongsTo
    {
        return $this->belongsTo(Vip::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeBySearchQuery($query) : Builder
    {
        if (! request()->has('query') || ! (bool) request()->get('query')) {
            return $query;
        }

        return $query->where('email', 'like', '%' . request()->get('query') . '%')
                    ->orWhere('first_name', 'like', '%' . request()->get('query') . '%')
                    ->orWhere('last_name', 'like', '%' . request()->get('query') . '%')
                    ->orWhere('phone_number', 'like', '%' . request()->get('query') . '%')
                    ->orderByDesc('id');
    }

    public function scopeForStaffIds($query, array $staffIds) : Builder
    {
        if (! $staffIds || ! count($staffIds)) {
            return $query;
        }

        return $query->whereHas('appointments', function ($q) use ($staffIds) {
            $q->whereIn('staff_id', $staffIds);
        });
    }

    public function scopeFiltered($query) : Builder
    {
        $request = request()->all();

        $staffIds = $request['staff_ids'] ?? [];
        $orderBy = $request['order_by'] ?? 'id';
        $sortBy = $request['sort_by'] ?? 'desc';

        return $query->forStaffIds($staffIds)
                    ->bySearchQuery()
                    ->where(function ($q) {
                        return $q->whereDoesntHave('staff')->orWhereHas('staff', function ($sq) {
                            if (auth()->user()->hasAccessTo('manage others', 'appointments')) {
                                return $sq;
                            }

                            return $sq->where('staff.id', auth()->user()->id);
                        });
                    })
                    ->orderBy($orderBy, $sortBy);
    }

    /**
     * Sends the user an email with a link to reset its password
     *
     * @param string $token
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        if (! $this->email) {
            return;
        }
        $url = config('app.client') . '/reset-password?token=' . $token . '&email=' . $this->email;
        Mail::to($this->email)->queue(new ResetPassword($this, $url));
    }

    /**
     * Sends newsletter approval confirmation
     *
     * @param string $token
     * @param string $token
     * @return void
     */
    public function sendNewsletterApprovalConfirmation(): void
    {
        if (! $this->email) {
            return;
        }
        Mail::to($this->email)->queue(new NewsletterApprovalEmail($this));
        $this->update(['newsletter_approval_at' => now()]);
    }

    public function updateFromRequest(CustomerRequest $request)
    {
        if ($request->accepts_newsletter) {
            // update user without the newsletter
            $this->update($request->except(['organisation_id', 'accepts_newsletter']));

            if ($this->declined_newsletter_at) {
                return;
            }
            // approval already sent.
            if ($this->newsletter_approval_at > now()->subDays(7)) {
                return;
            }
            // send email to the user
            $this->sendNewsletterApprovalConfirmation();
        } else {
            $this->update($request->except(['organisation_id']));
        }
    }
}
