<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Insurer extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo_url',
        'brand_color',
        'website_url',
        'description',
        'meta_title',
        'meta_description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Accessor: $insurer->logo (alias for logo_url, used in all Blade templates).
     */
    public function getLogoAttribute(): ?string
    {
        return $this->logo_url;
    }

    /**
     * Products (plans) offered by this insurer.
     */
    public function products(): HasMany
    {
        return $this->hasMany(InsurerProduct::class);
    }

    /**
     * Provinces where this insurer operates.
     */
    public function provinces(): BelongsToMany
    {
        return $this->belongsToMany(Province::class, 'insurer_province')
            ->using(InsurerProvince::class)
            ->withPivot([
                'id',
                'pdf_url',
                'pdf_local_path',
                'localities_covered',
                'specialties_available',
                'meta_title',
                'meta_description',
                'province_faqs',
                'content_html',
                'last_updated_at',
            ])
            ->withTimestamps();
    }

    /**
     * Special groups (MUFACE, MUGEJU, ISFAS) this insurer participates in.
     */
    public function specialGroups(): BelongsToMany
    {
        return $this->belongsToMany(SpecialGroup::class, 'insurer_special_group')
            ->withPivot(['id', 'pdf_url', 'description'])
            ->withTimestamps();
    }

    /**
     * FAQ items for this insurer.
     */
    public function faqs(): MorphMany
    {
        return $this->morphMany(FaqItem::class, 'faqable');
    }
}
