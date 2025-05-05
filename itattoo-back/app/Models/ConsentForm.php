<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConsentForm extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'consent_forms';

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
        'statements' => 'array',
        'sign_pad' => 'boolean',
        'status' => 'boolean',
        'use_custom_consent' => 'boolean',
        'infant_consent' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeByCategory($query)
    {
        $categoryId = request()->get('category_id');
        if (! (bool) $categoryId) {
            return $query;
        }

        return $query->whereCategoryId($categoryId);
    }

    public function scopeInOrganisation($query)
    {
        return $query->whereOrganisationId(auth()->user()->default_organisation_id);
    }

    public function scopeActive($query)
    {
        return $query->whereIsActive(1);
    }
}
