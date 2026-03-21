<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FaqItem extends Model
{
    protected $fillable = [
        'faqable_type',
        'faqable_id',
        'question',
        'answer',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * The parent model (Insurer, InsurerProduct, SpecialGroup, etc.).
     */
    public function faqable(): MorphTo
    {
        return $this->morphTo();
    }
}
