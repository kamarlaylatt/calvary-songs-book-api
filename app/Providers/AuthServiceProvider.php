<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\AppVersion;
use App\Models\Category;
use App\Models\Song;
use App\Models\SongLanguage;
use App\Models\Style;
use App\Policies\AdminPolicy;
use App\Policies\AppVersionPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\SongLanguagePolicy;
use App\Policies\SongPolicy;
use App\Policies\StylePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Admin::class => AdminPolicy::class,
        AppVersion::class => AppVersionPolicy::class,
        Category::class => CategoryPolicy::class,
        Song::class => SongPolicy::class,
        SongLanguage::class => SongLanguagePolicy::class,
        Style::class => StylePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
