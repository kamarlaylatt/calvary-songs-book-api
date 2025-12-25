<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SuggestSong extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_CANCELLED = 0;
    public const STATUS_PENDING = 1;
    public const STATUS_APPROVED = 2;

    protected $fillable = [
        'code',
        'title',
        'slug',
        'youtube',
        'description',
        'song_writer',
        'style_id',
        'key',
        'lyrics',
        'music_notes',
        'popular_rating',
        'createable_type',
        'createable_id',
        'email',
        'status',
    ];

    protected $casts = [
        'popular_rating' => 'integer',
        'status' => 'integer',
    ];

    public function createable()
    {
        return $this->morphTo();
    }

    public function style()
    {
        return $this->belongsTo(Style::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function songLanguages()
    {
        return $this->belongsToMany(SongLanguage::class);
    }

    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);

        $existingSlugs = static::where('slug', 'like', $baseSlug . '%')
            ->when($ignoreId, function ($query, $ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->pluck('slug');

        if (!$existingSlugs->contains($baseSlug)) {
            return $baseSlug;
        }

        $maxSuffix = $existingSlugs
            ->map(function ($existingSlug) use ($baseSlug) {
                if ($existingSlug === $baseSlug) {
                    return 0;
                }

                if (str_starts_with($existingSlug, $baseSlug . '-')) {
                    $suffix = substr($existingSlug, strlen($baseSlug) + 1);
                    return ctype_digit($suffix) ? (int) $suffix : null;
                }

                return null;
            })
            ->filter()
            ->max() ?? 0;

        return $baseSlug . '-' . ($maxSuffix + 1);
    }
}
