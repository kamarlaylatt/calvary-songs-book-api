<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SongSuggestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'youtube',
        'description',
        'song_writer',
        'style_id',
        'lyrics',
        'music_notes',
        'status',
        'user_id',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
