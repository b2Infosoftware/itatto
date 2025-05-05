<?php

namespace App\Models;

use App\Models\Customer;
use App\Mail\ResetPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PublicSlot extends Model
{
    use HasFactory;

    protected $table = 'public_slots';
    protected $guarded = [];
    protected $hidden = [
        'phone',
    ];

    protected $casts = [
        'date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'status_updated_by', 'id');
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'organisations_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sendPasswordResetNotification($token): void
    {
        $url = config('app.client') . '/reset-password?token=' . $token . '&email=' . $this->email;
        Mail::to($this->email)->queue(new ResetPassword($this, $url));
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBetweenTimes($query, $startTime, $endTime)
    {
        return $query->whereBetween('start_time', [$startTime, $endTime]);
    }
}
