<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class InsurerProduct extends Model
{
    protected $fillable = [
        'insurer_id',
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * The insurer that owns this product.
     */
    public function insurer(): BelongsTo
    {
        return $this->belongsTo(Insurer::class);
    }

    /**
     * FAQ items for this product.
     */
    public function faqs(): MorphMany
    {
        return $this->morphMany(FaqItem::class, 'faqable');
    }
}
