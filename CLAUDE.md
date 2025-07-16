# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview
This is a Laravel-based API project called "calvary-songs-book-api". The project appears to be designed for managing a songs book system with both admin and user functionality.

## Development Commands

### Running the Application
- `composer run dev` - Starts the development server with concurrent processes (server, queue, logs, and vite)
- `php artisan serve` - Starts the Laravel development server only
- `npm run dev` - Starts Vite development server for frontend assets

### Testing
- `composer test` - Runs the full test suite (clears config first, then runs tests)
- `php artisan test` - Runs tests directly via artisan

### Building
- `npm run build` - Builds frontend assets for production using Vite

### Code Quality
- Uses Laravel Pint for code formatting (available in composer.json)
- PHPUnit for testing with SQLite in-memory database for tests

## Architecture

### Directory Structure
- `app/Http/Controllers/Api/` - API controllers organized by Admin and User namespaces
- `app/Models/` - Eloquent models (currently includes User model)
- `routes/web.php` - Web routes (currently minimal)
- `database/migrations/` - Database migrations including users, cache, and jobs tables
- `resources/` - Frontend assets (CSS/JS) and Blade views
- `tests/` - PHPUnit tests split into Feature and Unit directories

### Key Technologies
- Laravel 12.0 (PHP 8.2+)
- Vite for asset compilation
- TailwindCSS 4.0 for styling
- SQLite for database (with in-memory testing)
- PHPUnit for testing

### Database
- Uses SQLite by default (`database/database.sqlite`)
- Migrations include standard Laravel auth tables plus cache and jobs
- Testing uses in-memory SQLite database

### Frontend
- Uses Vite with Laravel plugin
- TailwindCSS for styling
- Entry points: `resources/css/app.css` and `resources/js/app.js`