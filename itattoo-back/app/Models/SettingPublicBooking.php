<?php

namespace App\Models;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SettingPublicBooking extends Model
{
    use HasFactory;

    protected $table = 'setting_public_booking';

    protected $fillable = [
        'is_open', 'organisation_id'
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }
}