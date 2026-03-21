<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Province extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'autonomous_community',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Insurers operating in this province.
     */
    public function insurers(): BelongsToMany
    {
        return $this->belongsToMany(Insurer::class, 'insurer_province')
            ->using(InsurerProvince::class)
            ->withPivot([
                'id',
                'pdf_url',
                'pdf_local_path',
                'localities_covered',
                'specialties_available',
                'meta_title',
                'meta_description',
                'last_updated_at',
            ])
            ->withTimestamps();
    }
}
