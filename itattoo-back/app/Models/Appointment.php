<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Traits\HasNotifications;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\OrganisationAppointments;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory, HasNotifications;

    protected $with = ['service', 'customer', 'project', 'location', 'staff'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'appointments';

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
        'sms_reminder_at' => 'timestamp',
        'email_reminder_at' => 'timestamp',
        'date' => 'string',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new OrganisationAppointments);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class)->withTrashed();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function document(): MorphOne
    {
        return $this->morphOne(SignedDocument::class, 'documentable');
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

    /**
     * Gets a the position of this appointment inside of a project
     */
    public function getProjectAppointments()
    {
        return self::whereProjectId($this->project_id)->with('project.category')->orderBy('date')->orderBy('start_time')->get()->map(function ($item, $index) {
            return [
                'id'=> $item->id,
                'index'=> $index + 1,
                'date' => $item->date,
                'start_time' => $item->start_time,
                'deposit' => $item->deposit,
                'project_name' => $item->project->name,
                'project_category' =>$item->project->category->name,
            ];
        });
    }

    /**
     * Generates a cancel url for the customer to use
     *
     * @return void
     */
    public function generateCancelUrl()
    {
        return config('app.client') . '/cancel-appointment/' . $this->id;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'canceled')->orWhereNull('status');
    }

    public function scopeUpcoming($query)
    {
        return $query->active()->where('date', '>', today());
    }

    public function scopeIncomplete($query)
    {
        return $query->whereNull('status')->orWhereIn('status', ['completed_unpaid', 'advance_payment']);
    }

    public function scopeForStaffIds($query, array $staffIds)
    {
        if (! $staffIds || ! count($staffIds)) {
            return $query;
        }

        return $query->whereIn('staff_id', $staffIds);
    }

    public function scopeForCustomerIds($query, array $customerIds)
    {
        if (! $customerIds || ! count($customerIds)) {
            return $query;
        }

        return $query->whereIn('customer_id', $customerIds);
    }

    public function scopeForServiceIds($query, array $serviceIds)
    {
        if (! $serviceIds || ! count($serviceIds)) {
            return $query;
        }

        return $query->whereIn('service_id', $serviceIds);
    }

    public function scopeForStatus($query, $status)
    {
        if (is_null($status)) {
            return $query->whereNull('status');
        }

        if (! $status) {
            return $query->active();
        }

        return $query->whereStatus($status);
    }

    public function scopeByDuration($query, $operator, $duration)
    {
        if (! $duration) {
            return $query;
        }
        if (! in_array($operator, ['=', '>', '>=', '<', '<='])) {
            return $query;
        }

        return $query->where('duration', $operator, $duration);
    }

    public function scopeFiltered($query, $ignoreInterval = false)
    {
        $request = request()->all();

        $start = $request['from'] ? Carbon::createFromFormat('Y-m-d', $request['from'])->startOfWeek() : now()->startOfWeek();
        $end = $request['to'] ? Carbon::createFromFormat('Y-m-d', $request['to'])->endOfDay() : $start->copy()->endOfWeek();

        $staffIds = $request['staff_ids'] ?? [];
        $customerIds = $request['customer_ids'] ?? [];
        $serviceIds = $request['service_ids'] ?? [];
        $status = $request['status'] ?? false;
        $duration = $request['duration'] ?? false;
        $operator = $request['duration_operator'] ?? '=';

        $query = $ignoreInterval ? $query->upcoming() : $query->whereBetween('date', [$start, $end]);

        return $query->forStaffIds($staffIds)
                    ->forCustomerIds($customerIds)
                    ->forServiceIds($serviceIds)
                    ->forStatus($status)
                    ->byDuration($operator, $duration);
    }

    public function scopeSearchByCustomerInfo($query, $info)
    {
        if (! $info || ! strlen($info)) {
            return $query;
        }

        return $query->whereHas('customer', function ($q) use ($info) {
            return $q->where('first_name', 'like', '%' . $info . '%')
                    ->orWhere('last_name', 'like', '%' . $info . '%')
                    ->orWhere('phone_number', 'like', '%' . $info . '%');
        });
    }
}
