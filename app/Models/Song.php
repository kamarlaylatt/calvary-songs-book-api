<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Song extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'title',
        'slug',
        'youtube',
        'description',
        'song_writer',
        'style_id',
        'lyrics',
        'music_notes',
        'popular_rating',
        'createable_type',
        'createable_id',
    ];

    protected $casts = [
        'popular_rating' => 'integer',
    ];

    public static function rules()
    {
        return [
            'popular_rating' => 'integer|min:0|max:5',
        ];
    }

    public static function generateSlug(string $title, int $code): string
    {
        return Str::slug($title).'-'.$code;
    }

    public static function nextCode(bool $lockForUpdate = false): int
    {
        if ($lockForUpdate) {
            // For PostgreSQL compatibility, lock actual rows instead of using aggregate with lock
            $maxCode = static::query()
                ->orderByDesc('code')
                ->lockForUpdate()
                ->value('code');
        } else {
            $maxCode = static::max('code');
        }

        return (int) ($maxCode ?? 0) + 1;
    }

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

    public function hymns()
    {
        return $this->hasMany(Hymn::class);
    }
}
