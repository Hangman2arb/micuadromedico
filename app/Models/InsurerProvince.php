<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class InsurerProvince extends Pivot
{
    protected $table = 'insurer_province';

    public $incrementing = true;

    protected $fillable = [
        'insurer_id',
        'province_id',
        'pdf_url',
        'pdf_local_path',
        'localities_covered',
        'specialties_available',
        'meta_title',
        'meta_description',
        'last_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'localities_covered' => 'array',
            'specialties_available' => 'array',
            'last_updated_at' => 'datetime',
        ];
    }

    /**
     * The insurer in this pivot.
     */
    public function insurer(): BelongsTo
    {
        return $this->belongsTo(Insurer::class);
    }

    /**
     * The province in this pivot.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }
}
