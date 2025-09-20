<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'platform',
        'version_code',
        'version_name',
        'update_url',
        'release_notes',
        'is_force_update',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_force_update' => 'boolean',
    ];

    /**
     * Get the latest version for a specific platform
     */
    public static function getLatestVersion(string $platform)
    {
        return self::where('platform', $platform)
            ->orderBy('version_code', 'desc')
            ->first();
    }

    /**
     * Get the minimum version that requires force update
     */
    public static function getMinimumForceUpdateVersion(string $platform)
    {
        return self::where('platform', $platform)
            ->where('is_force_update', true)
            ->orderBy('version_code', 'desc')
            ->first();
    }
}
