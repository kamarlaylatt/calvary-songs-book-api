<?php

namespace App\Models;

use App\Enums\AdminRoleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $casts = [
        'id' => AdminRoleType::class,
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
