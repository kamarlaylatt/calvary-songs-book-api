<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hymn extends Model
{
    /** @use HasFactory<\Database\Factories\HymnFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'composer',
        'hymn_category_id',
    ];

    /**
     * Get the category that owns the hymn.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<HymnCategory, Hymn>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(HymnCategory::class, 'hymn_category_id');
    }

    /**
     * Get all of the details for the hymn.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<HymnDetail>
     */
    public function details(): HasMany
    {
        return $this->hasMany(HymnDetail::class);
    }
}
