<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HymnCategory extends Model
{
    /** @use HasFactory<\Database\Factories\HymnCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get all of the hymns for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Hymn>
     */
    public function hymns(): HasMany
    {
        return $this->hasMany(Hymn::class);
    }
}
