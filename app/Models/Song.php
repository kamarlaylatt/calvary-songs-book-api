<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
