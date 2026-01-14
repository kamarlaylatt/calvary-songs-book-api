<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hymn extends Model
{
    /** @use HasFactory<\Database\Factories\HymnFactory> */
    use HasFactory;

    protected $fillable = [
        'no',
        'hymn_category_id',
        'song_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'no' => 'integer',
        ];
    }

    public function hymnCategory(): BelongsTo
    {
        return $this->belongsTo(HymnCategory::class);
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }
}
