# System Architecture

## Backend Framework

-   Laravel 12 (PHP 8.2) application structured with streamlined bootstrap configuration.
-   Stateless REST API footprint leveraging Sanctum token authentication for both admin and user channels.

## Application Layers

-   **HTTP Controllers**
    -   Admin namespace: `[AuthController](app/Http/Controllers/Api/Admin/AuthController.php)`, `[AdminController](app/Http/Controllers/Api/Admin/AdminController.php)`, `[SongController](app/Http/Controllers/Api/Admin/SongController.php)`, `[CategoryController](app/Http/Controllers/Api/Admin/CategoryController.php)`, `[StyleController](app/Http/Controllers/Api/Admin/StyleController.php)`, `[SongLanguageController](app/Http/Controllers/Api/Admin/SongLanguageController.php)`.
    -   User namespace: `[AuthController](app/Http/Controllers/Api/User/AuthController.php)`, `[SongController](app/Http/Controllers/Api/User/SongController.php)`, `[VersionController](app/Http/Controllers/Api/User/VersionController.php)`.
-   **Routes**
    -   `[routes/api.php](routes/api.php)` mounts admin API under `/api/admin` and public user endpoints.
    -   `[routes/admin.php](routes/admin.php)` declares protected admin resources behind `auth:admin`.
    -   Web layer limited to static pages (`[routes/web.php](routes/web.php)`).
-   **Policies & Authorization**
    -   `[AuthServiceProvider](app/Providers/AuthServiceProvider.php)` maps models to `[AdminPolicy](app/Policies/AdminPolicy.php)`, `[CategoryPolicy](app/Policies/CategoryPolicy.php)`, `[SongPolicy](app/Policies/SongPolicy.php)`, `[SongLanguagePolicy](app/Policies/SongLanguagePolicy.php)`, `[StylePolicy](app/Policies/StylePolicy.php)`.
    -   Policies guard create/update/delete, disallow actions for Guest role.

## Domain Model

-   Core Eloquent models: `[Song](app/Models/Song.php)`, `[Category](app/Models/Category.php)`, `[Style](app/Models/Style.php)`, `[SongLanguage](app/Models/SongLanguage.php)`, `[Admin](app/Models/Admin.php)`, `[User](app/Models/User.php)`, `[Role](app/Models/Role.php)`, `[AppVersion](app/Models/AppVersion.php)`.
-   Relationships:
    -   Songs belong to Styles and support many-to-many with Categories & SongLanguages; created by morph-to `createable` (Admin/User).
    -   Admins have many Songs (morph) and belong to many Roles (`admin_role` pivot).
    -   Categories, SongLanguages maintain pivot tables `[category_song](database/migrations/2025_07_18_102133_rename_song_category_to_category_song.php)` and `[song_song_language](database/migrations/2025_08_12_033353_create_song_song_language_table.php)`.
-   Enums: `[AdminRoleType](app/Enums/AdminRoleType.php)` ensures canonical role identities.

## Caching & Performance

-   User song listings and individual song payloads cached via `Cache::remember` in `[app/Http/Controllers/Api/User/SongController.php](app/Http/Controllers/Api/User/SongController.php)`.
-   Admin song CRUD clears cache with `[SongController::clearSongCaches()](app/Http/Controllers/Api/Admin/SongController.php:166)` by flushing store.
-   Search-filter lookup cached for 15 minutes.

## Data Validation & Pagination

-   Controllers rely on request validation directly within methods.
-   Admin listing endpoints paginate (default 15 for songs, 10 for admins/categories).
-   User listing returns paginated or collection responses based on `limit` query parameter.

## Commands & Tooling

-   `[ImportSongs](app/Console/Commands/ImportSongs.php)` ingests CSV files, auto-creating styles and songs under Admin user.
-   `[DbDumpCommand](app/Console/Commands/DbDumpCommand.php)` generates Postgres dumps to `storage/app/db-dumps`.

## Database Schema

-   Schema migrations live under `[database/migrations](database/migrations)` covering users, admins (with soft deletes & status), roles, songs (code, slug, key, popular rating, full-text indexes), categories, styles, song_languages, pivots, app versions.
-   Seeders `[RoleSeeder](database/seeders/RoleSeeder.php)`, `[AdminSeeder](database/seeders/AdminSeeder.php)`, `[CategorySeeder](database/seeders/CategorySeeder.php)`, `[SongLanguageSeeder](database/seeders/SongLanguageSeeder.php)`, `[AppVersionSeeder](database/seeders/AppVersionSeeder.php)` populate baseline data executed via `[DatabaseSeeder](database/seeders/DatabaseSeeder.php)`.

## Testing

-   Feature coverage anchored by `[AdminControllerTest](tests/Feature/AdminControllerTest.php)` validating CRUD, validation errors, and soft deletes for admin management.
-   `RefreshDatabase` trait ensures isolated state per test run.

## API Documentation

-   Formal specs maintained in `[ADMIN_API_DOCUMENTATION.MD](ADMIN_API_DOCUMENTATION.MD)` and `[USER_API_DOCUMENTATION.MD](USER_API_DOCUMENTATION.MD)` aligning with implemented routes.
