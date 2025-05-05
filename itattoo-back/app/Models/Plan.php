<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'stripe_id', 'price', 'months', 'is_artist', 'members', 'is_marketing_modul', 'is_sms', 'max_staff_members'];
}
