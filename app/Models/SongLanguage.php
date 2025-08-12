<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SongLanguage extends Model
{
    protected $fillable = ['name'];

    public function songs()
    {
        return $this->belongsToMany(Song::class);
    }
}
