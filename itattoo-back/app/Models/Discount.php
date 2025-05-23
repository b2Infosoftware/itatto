<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'discount_type',
        'discount_value',
        'vip_id'
    ];

    public function ExclusiveOffer()
    {
        return $this->belongsTo(ExclusiveOffer::class);
    }
}
