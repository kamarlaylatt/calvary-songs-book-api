# Technology Stack

## Languages & Frameworks

-   PHP 8.2 running Laravel 12 for backend API.
-   JavaScript tooling via Vite with ES modules; TailwindCSS 4 for styling.
-   PHPUnit 11.x for automated testing.

## Key Packages & Libraries

-   `laravel/framework` ^12.0: core application framework.
-   `laravel/sanctum` ^4.1: API token authentication for admin and user channels.
-   `laravel/nightwatch` ^1.11: end-to-end browser testing scaffolding.
-   `laravel/tinker` ^2.10.1: interactive REPL for debugging.
-   Dev dependencies: `laravel/pint` ^1.13 (code style), `laravel/boost` ^1.0 (MCP helper), `laravel/pail` ^1.2.2 (log tailing), `laravel/sail` ^1.44 (local env scaffolding), `phpunit/phpunit` ^11.5.3, `mockery/mockery` ^1.6, `nunomaduro/collision` ^8.6, `fakerphp/faker` ^1.23.
-   Frontend build: `vite` ^6.2.4, `laravel-vite-plugin` ^1.2.0, `@tailwindcss/vite` ^4.0.0, `axios` ^1.8.2, `concurrently` ^9.0.1.

## Tooling & Scripts

-   Composer scripts:
    -   `composer run dev`: concurrent processes for artisan serve, queue listener, pail, and `npm run dev`.
    -   `composer test`: clears config cache then runs `php artisan test`.
    -   Post create/install hooks auto copy `.env`, touch SQLite database, run migrations.
-   NPM scripts: `npm run dev` (Vite dev server), `npm run build` (Vite production build).

## Configuration & Environment

-   `.env.example` provided; `composer` post-install copies to `.env`.
-   Default database driver configured for SQLite (testing) with support for Postgres (db dump command).
-   Sanctum guards defined for `api` (users) and `admin` channels in `[config/auth.php](config/auth.php)`.
-   Cache configured via `[config/cache.php](config/cache.php)`; application leverages Cache facade for song listings.
-   Filesystem storage at `storage/app` with public symlink; DB dumps written to `storage/app/db-dumps`.

## Testing & Quality

-   Tests placed under `tests/Feature` and `tests/Unit`; `AdminControllerTest` covers admin CRUD flows.
-   `RefreshDatabase` trait ensures clean state per test case.
-   Pint (`vendor/bin/pint`) used for PSR-12 style conformance.

## Deployment & Ops Considerations

-   Database migrations include soft deletes and full-text indexes for songs.
-   `DbDumpCommand` executes `pg_dump` for backup; requires Postgres CLI tools.
-   CSV import command expects `songs.csv` format and creates related styles automatically.
