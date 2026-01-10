<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HymnDetail extends Model
{
    /** @use HasFactory<\Database\Factories\HymnDetailFactory> */
    use HasFactory;

    protected $fillable = [
        'hymn_book_id',
        'no',
        'hymn_id',
        'title',
        'key',
        'scriptures',
        'lyrics',
        'pdf_file',
        'pptx_file',
    ];

    /**
     * Get the hymn book that owns the detail.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<HymnBook, HymnDetail>
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(HymnBook::class, 'hymn_book_id');
    }

    /**
     * Get the hymn that owns the detail.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Hymn, HymnDetail>
     */
    public function hymn(): BelongsTo
    {
        return $this->belongsTo(Hymn::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'scriptures' => 'array',
            'lyrics' => 'array',
        ];
    }
}
