<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HymnCategory extends Model
{
    /** @use HasFactory<\Database\Factories\HymnCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function hymns()
    {
        return $this->hasMany(Hymn::class);
    }
}
