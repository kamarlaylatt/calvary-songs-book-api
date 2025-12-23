<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuggestSong extends Model
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
}
