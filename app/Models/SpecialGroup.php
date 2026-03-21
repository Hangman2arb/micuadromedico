<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SpecialGroup extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Insurers participating in this special group.
     */
    public function insurers(): BelongsToMany
    {
        return $this->belongsToMany(Insurer::class, 'insurer_special_group')
            ->withPivot(['id', 'pdf_url', 'description'])
            ->withTimestamps();
    }

    /**
     * FAQ items for this special group.
     */
    public function faqs(): MorphMany
    {
        return $this->morphMany(FaqItem::class, 'faqable');
    }
}
