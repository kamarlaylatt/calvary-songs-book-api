<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HymnBook extends Model
{
    /** @use HasFactory<\Database\Factories\HymnBookFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get all of the details for the hymn book.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<HymnDetail>
     */
    public function hymnDetails(): HasMany
    {
        return $this->hasMany(HymnDetail::class);
    }
}
