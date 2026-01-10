# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview
This is a Laravel-based REST API for managing a songs book system with dual functionality:
- **User API**: Public endpoints for song browsing, searching, and user authentication
- **Admin API**: Protected endpoints for managing songs, categories, styles, and users
- **Hymns System**: Recently added system for managing hymn books, categories, and hymn details

## Development Commands

### Running the Application
- `composer run dev` - Starts development server with concurrent processes (server, queue, logs, vite)
- `php artisan serve` - Starts Laravel development server only
- `npm run dev` - Starts Vite development server for frontend assets

### Testing
- `composer test` - Runs full test suite (clears config first, then runs tests)
- `php artisan test` - Runs tests directly via artisan
- Tests use in-memory SQLite database

### Code Quality
- `vendor/bin/pint` - Run Laravel Pint to format code (run this before finalizing changes)
- Do not use `vendor/bin/pint --test` - just run `vendor/bin/pint` to fix formatting issues

### Building
- `npm run build` - Builds frontend assets for production using Vite

## Architecture

### Directory Structure
- `app/Http/Controllers/Api/User/` - User-facing API controllers
- `app/Http/Controllers/Api/Admin/` - Admin API controllers
- `app/Models/` - Eloquent models (User, Admin, Song, Category, Style, SongLanguage, AppVersion, SuggestSong, Role, and Hymn models)
- `routes/api.php` - User API routes
- `routes/admin.php` - Admin API routes
- `routes/web.php` - Web routes (minimal)
- `routes/console.php` - Console routes
- `bootstrap/app.php` - Application configuration (middleware, routing, providers)
- `bootstrap/providers.php` - Application service providers

### Authentication Architecture
**Dual Authentication System:**
- **User Authentication**: Laravel Sanctum with `auth:api` middleware
- **Admin Authentication**: Custom guard with `auth:admin` middleware (configured via middleware alias in `bootstrap/app.php`)
- **Authorization**: Policy-based authorization for all models
- **Roles**: Admin roles (Superadmin, Admin, Guest) via enum

### Database Models & Relationships

**Core Models:**
- **User**: Standard user with Sanctum tokens, has morphMany relationship to Songs
- **Admin**: Extended user with soft deletes, roles, and morphMany relationship to Songs
- **Role**: Enum-based roles (Superadmin, Admin, Guest)
- **Song**: Central model with polymorphic ownership (createable), categories, languages, and styles
- **Category**: Hierarchical categorization with sort ordering, soft deletes
- **Style/SongLanguage**: Taxonomy models for song classification
- **AppVersion**: Version control for force app updates
- **SuggestSong**: Song suggestions from mobile app users

**Hymn System Models (Recent):**
- **HymnCategory**: Categories for hymns
- **HymnBook**: Collection of hymns
- **Hymn**: Individual hymns with composer and category
- **HymnDetail**: Verses/sections of hymns

**Key Relationships:**
- Polymorphic: Songs can be created by Users or Admins (`createable` polymorphic relation)
- Many-to-many: Songs-Categories, Songs-SongLanguages
- Soft Deletes: Enabled on Admins and Categories

### API Structure Patterns

**Controller Organization:**
- Separated into `Api\User` and `Api\Admin` namespaces
- Consistent RESTful patterns with resource controllers
- Form Request validation classes for validation (not inline)
- Eager loading to prevent N+1 queries

**Caching Strategy:**
- Extensive use of Laravel Cache for performance
- Song listings: 30 minutes
- Song details: 15 minutes
- Search filters: 15 minutes
- Cache keys based on request parameters

**Response Patterns:**
- Consistent JSON responses with standardized structure
- API Resources used for transformations
- Pagination support with configurable limits

### Key Technologies
- **Framework**: Laravel 12.0 (PHP 8.2+)
- **Authentication**: Laravel Sanctum
- **Database**: SQLite (configurable for MySQL/PostgreSQL)
- **Testing**: PHPUnit with in-memory SQLite
- **Frontend Build**: Vite with Laravel Vite Plugin
- **CSS**: TailwindCSS 4.0
- **Code Quality**: Laravel Pint

### Special Features

**Song Suggestions:**
- Public endpoint for mobile app users
- Admin approval workflow
- Polymorphic relationship to categories/languages

**Version Control:**
- App version management for force updates
- Version checking endpoint

**Search Capabilities:**
- Full-text search on song titles and lyrics
- Multi-filter support (style, category, language)
- Search filter caching for performance

## Laravel 12 Specifics

This project uses the new Laravel 11/12 streamlined structure:
- No middleware files in `app/Http/Middleware/` (registered in `bootstrap/app.php`)
- No `app\Console\Kernel.php` (use `bootstrap/app.php` or `routes/console.php`)
- Commands auto-register from `app/Console/Commands/`
- Casts defined in `casts()` method rather than `$casts` property
- When modifying columns in migrations, include all previously defined attributes

## Testing Conventions

- Use factories for creating models in tests
- Follow existing conventions for `$this->faker` vs `fake()`
- Most tests should be feature tests (use `php artisan make:test`, not `--unit`)
- Check for custom states in factories before manually setting up models
