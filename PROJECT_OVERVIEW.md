# Calvary Songs Book API - Project Overview

## 📖 Table of Contents
- [Introduction](#introduction)
- [Project Purpose](#project-purpose)
- [Technology Stack](#technology-stack)
- [Architecture](#architecture)
- [Database Schema](#database-schema)
- [API Endpoints](#api-endpoints)
- [Authentication & Authorization](#authentication--authorization)
- [Key Features](#key-features)
- [Development Setup](#development-setup)
- [Testing](#testing)
- [Deployment](#deployment)

## Introduction

**Calvary Songs Book API** is a Laravel-based RESTful API designed to manage and serve a digital songs book/hymnal system. The application provides comprehensive song management capabilities with separate interfaces for administrators and end users.

This project is built with Laravel 12.0 and uses SQLite as its default database, making it lightweight and easy to deploy while maintaining full database functionality.

## Project Purpose

The Calvary Songs Book API serves as the backend for a digital hymnal/songbook application. It enables:

1. **For Administrators:**
   - Complete CRUD operations for songs, categories, styles, and languages
   - User and admin management with role-based access control
   - Content organization and curation
   - Version management for mobile applications

2. **For End Users:**
   - Browse and search songs by various criteria (title, lyrics, category, style, language)
   - View detailed song information including lyrics, music notes, YouTube links
   - Access songs through a clean, paginated API
   - Mobile app version checking for forced updates

## Technology Stack

### Backend Framework
- **Laravel 12.0** - Modern PHP web application framework
- **PHP 8.2+** - Latest PHP version with enhanced performance and type safety

### Database
- **SQLite** - Lightweight, serverless database (default)
- **Doctrine DBAL** - Database abstraction layer for schema management
- Supports other databases (MySQL, PostgreSQL) via configuration

### Authentication
- **Laravel Sanctum 4.1** - Token-based API authentication for both users and admins

### Frontend Assets
- **Vite 6.2.4** - Modern frontend build tool
- **TailwindCSS 4.0** - Utility-first CSS framework
- **Laravel Vite Plugin** - Seamless integration between Laravel and Vite

### Development Tools
- **PHPUnit 11.5** - Testing framework
- **Laravel Pint** - Opinionated PHP code style fixer
- **Composer** - PHP dependency management
- **NPM** - JavaScript package management

## Architecture

### Project Structure

```
calvary-songs-book-api/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           ├── Admin/          # Admin-specific controllers
│   │           │   ├── AuthController.php
│   │           │   ├── SongController.php
│   │           │   ├── CategoryController.php
│   │           │   ├── StyleController.php
│   │           │   ├── SongLanguageController.php
│   │           │   ├── AdminController.php
│   │           │   └── RoleController.php
│   │           └── User/           # User-facing controllers
│   │               ├── AuthController.php
│   │               ├── SongController.php
│   │               └── VersionController.php
│   └── Models/
│       ├── Admin.php              # Admin user model
│       ├── User.php               # End user model
│       ├── Song.php               # Main song model
│       ├── Category.php           # Song categories
│       ├── Style.php              # Song styles
│       ├── SongLanguage.php       # Song languages
│       ├── Role.php               # Admin roles
│       └── AppVersion.php         # App version management
├── database/
│   ├── migrations/                # Database schema migrations
│   ├── factories/                 # Model factories for testing
│   └── seeders/                   # Database seeders
├── routes/
│   ├── api.php                    # User API routes
│   └── admin.php                  # Admin API routes
├── tests/                         # PHPUnit tests
├── public/                        # Public assets
└── resources/                     # Views and frontend assets
```

### Design Patterns

1. **RESTful API Architecture** - Standard REST principles for resource management
2. **Repository Pattern** - Eloquent ORM provides abstraction over database operations
3. **Middleware Pattern** - Authentication and authorization via middleware
4. **Service Container** - Dependency injection throughout the application
5. **MVC Pattern** - Model-View-Controller separation of concerns

## Database Schema

### Core Tables

#### 1. **songs**
Main table storing song information:
- `id` - Primary key
- `admin_id` - Foreign key to admin who created the song
- `style_id` - Foreign key to style (nullable)
- `code` - Integer song code
- `slug` - URL-friendly identifier
- `title` - Song title
- `youtube` - YouTube video link
- `description` - Song description
- `song_writer` - Composer/writer name
- `lyrics` - Full song lyrics (fulltext indexed)
- `music_notes` - Musical notation
- `popular_rating` - Popularity score (1-5)
- Timestamps (created_at, updated_at)

#### 2. **categories**
Song categorization:
- `id` - Primary key
- `name` - Category name
- `slug` - URL-friendly identifier
- `description` - Category description
- `sort_no` - Display order
- Timestamps

#### 3. **styles**
Musical styles:
- `id` - Primary key
- `name` - Style name (e.g., "Hymn", "Contemporary", "Gospel")
- Timestamps

#### 4. **song_languages**
Language options:
- `id` - Primary key
- `name` - Language name (e.g., "English", "Spanish")
- Timestamps

#### 5. **admins**
Administrative users:
- `id` - Primary key
- `name` - Admin name
- `email` - Admin email (unique)
- `password` - Hashed password
- `status` - Account status (active/inactive)
- `deleted_at` - Soft delete timestamp
- Timestamps

#### 6. **users**
End users:
- `id` - Primary key
- `name` - User name
- `email` - User email (unique)
- `password` - Hashed password
- Timestamps

#### 7. **roles**
Admin role definitions:
- `id` - Primary key
- `name` - Role name (e.g., "Super Admin", "Editor")
- Timestamps

#### 8. **app_versions**
Mobile app version management:
- `id` - Primary key
- `platform` - "android" or "ios"
- `version_code` - Integer version code
- `version_name` - Human-readable version (e.g., "1.3.0")
- `minimum_version_code` - Minimum required version for forced updates
- `update_url` - Store URL for app download
- `release_notes` - Update description
- `is_active` - Whether this version is active
- Timestamps

### Pivot Tables (Many-to-Many Relationships)

#### 1. **category_song**
Links songs to categories:
- `song_id` - Foreign key to songs
- `category_id` - Foreign key to categories

#### 2. **song_song_language**
Links songs to languages:
- `song_id` - Foreign key to songs
- `song_language_id` - Foreign key to song_languages

#### 3. **admin_role**
Links admins to roles:
- `admin_id` - Foreign key to admins
- `role_id` - Foreign key to roles

### Database Features

- **Full-text Search** - Songs table has fulltext index on title and lyrics for efficient searching
- **Soft Deletes** - Admins can be soft-deleted for audit trail
- **Foreign Key Constraints** - Maintains referential integrity
- **Indexes** - Optimized for common query patterns

## API Endpoints

### User Endpoints (`/api`)

#### Authentication
- `POST /api/login` - User login
- `POST /api/logout` - User logout (authenticated)
- `GET /api/user` - Get current user details (authenticated)

#### Songs (Public)
- `GET /api/songs` - List/search songs (supports pagination and filtering)
  - Query params: `search`, `style_id`, `category_id`, `song_language_id`, `limit`
- `GET /api/songs/{slug}` - Get single song by slug

#### Categories
- `GET /api/categories` - List all categories

#### Search Filters
- `GET /api/search-filters` - Get categories, styles, and languages for filtering

#### Version Management
- `POST /api/check-version` - Check if app needs update
  - Body: `{ "version_code": 1, "platform": "android" }`

### Admin Endpoints (`/api/admin`)

#### Authentication
- `POST /api/admin/login` - Admin login
- `POST /api/admin/logout` - Admin logout (authenticated)
- `GET /api/admin` - Get current admin details (authenticated)

#### Songs Management
- `GET /api/admin/songs` - List songs (paginated, with filters)
- `POST /api/admin/songs` - Create song
- `GET /api/admin/songs/{id}` - Get song details
- `PUT/PATCH /api/admin/songs/{id}` - Update song
- `DELETE /api/admin/songs/{id}` - Delete song

#### Categories Management
- `GET /api/admin/categories` - List categories (paginated)
- `POST /api/admin/categories` - Create category
- `GET /api/admin/categories/{id}` - Get category
- `PUT/PATCH /api/admin/categories/{id}` - Update category
- `DELETE /api/admin/categories/{id}` - Delete category

#### Styles Management
- `GET /api/admin/styles` - List all styles
- `POST /api/admin/styles` - Create style
- `GET /api/admin/styles/{id}` - Get style
- `PUT/PATCH /api/admin/styles/{id}` - Update style
- `DELETE /api/admin/styles/{id}` - Delete style

#### Song Languages Management
- `GET /api/admin/song-languages` - List all languages
- `POST /api/admin/song-languages` - Create language
- `GET /api/admin/song-languages/{id}` - Get language
- `PUT/PATCH /api/admin/song-languages/{id}` - Update language
- `DELETE /api/admin/song-languages/{id}` - Delete language

#### Admin Management
- `GET /api/admin/admins` - List admins (paginated)
- `POST /api/admin/admins` - Create admin
- `GET /api/admin/admins/{id}` - Get admin
- `PUT/PATCH /api/admin/admins/{id}` - Update admin
- `DELETE /api/admin/admins/{id}` - Delete admin (soft delete)

#### Roles
- `GET /api/admin/roles` - List all roles

## Authentication & Authorization

### Two Separate Authentication Guards

1. **User Authentication (`auth:api`)**
   - Uses Laravel Sanctum tokens
   - For regular end users accessing the song database
   - Token obtained via `/api/login`

2. **Admin Authentication (`auth:admin`)**
   - Uses Laravel Sanctum tokens with separate guard
   - For administrators managing content
   - Token obtained via `/api/admin/login`
   - Role-based access control via admin-role relationships

### Security Features

- Password hashing using bcrypt (configurable rounds)
- Token-based authentication (stateless)
- Separate authentication contexts for users and admins
- Soft deletes for admin accounts (audit trail)
- Status field for admin accounts (active/inactive)

## Key Features

### 1. Advanced Song Search
- **Full-text search** on song titles and lyrics
- **Multi-criteria filtering**:
  - By style (musical genre)
  - By category (theme/purpose)
  - By language
  - By ID or code
- **Flexible pagination** - Can return all results or paginated

### 2. Multi-Language Support
- Songs can be associated with multiple languages
- Useful for multilingual congregations
- Filterable by language in search

### 3. Rich Song Metadata
- YouTube video links
- Song writer/composer information
- Musical notation (music_notes field)
- Popularity ratings
- Auto-generated slugs for SEO-friendly URLs

### 4. Category Organization
- Songs can belong to multiple categories
- Categories have customizable sort order (`sort_no`)
- Hierarchical organization for better content discovery

### 5. Admin Role System
- Multiple admin roles supported
- Admins can have multiple roles
- Flexible permission structure
- Audit trail via created/updated timestamps

### 6. App Version Control
- Forced update mechanism for mobile apps
- Separate version tracking for Android and iOS
- Configurable minimum version requirements
- Custom update messages and release notes

### 7. Auto-Generated Fields
- **Slugs** - Automatically generated from titles for SEO
- **Song codes** - Auto-incrementing integer codes
- **Timestamps** - Automatic created_at and updated_at

## Development Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and NPM
- SQLite (or other database)

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/kamarlaylatt/calvary-songs-book-api.git
   cd calvary-songs-book-api
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

6. **Seed the database (optional)**
   ```bash
   php artisan db:seed
   ```

### Running the Application

#### Development Server (All-in-One)
```bash
composer run dev
```
This starts:
- Laravel development server (`:8000`)
- Queue worker
- Log viewer (Pail)
- Vite development server

#### Individual Services
```bash
# Laravel server only
php artisan serve

# Frontend assets
npm run dev

# Run tests
composer test
# or
php artisan test
```

## Testing

### Test Infrastructure
- **PHPUnit 11.5** for unit and feature tests
- **SQLite in-memory database** for isolated test environment
- **Faker** for generating test data
- **Mockery** for mocking dependencies

### Running Tests
```bash
# Via composer (clears config first)
composer test

# Via artisan
php artisan test

# With coverage
php artisan test --coverage
```

### Test Organization
- `tests/Feature/` - Integration tests for API endpoints
- `tests/Unit/` - Unit tests for individual components

## Deployment

### Production Considerations

1. **Database**
   - Consider using MySQL or PostgreSQL for production
   - Update `.env` with production database credentials
   - Run migrations on production database

2. **Environment**
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   ```

3. **Optimization**
   ```bash
   # Cache configuration
   php artisan config:cache
   
   # Cache routes
   php artisan route:cache
   
   # Cache views
   php artisan view:cache
   
   # Build frontend assets
   npm run build
   ```

4. **Queue Management**
   - Set up a proper queue driver (Redis, SQS, etc.)
   - Run queue workers via supervisor or similar

5. **Security**
   - Use HTTPS in production
   - Set strong `APP_KEY`
   - Configure CORS properly
   - Implement rate limiting
   - Regular security updates

### Docker Support
The project includes `docker-compose.yml` for containerized deployment:
```bash
docker-compose up -d
```

## Project Files of Note

### Documentation
- **README.md** - Laravel framework information
- **ADMIN_API_DOCUMENTATION.MD** - Complete admin API reference
- **USER_API_DOCUMENTATION.MD** - Complete user API reference
- **CLAUDE.md** - AI assistant guidance for development

### Configuration
- **composer.json** - PHP dependencies and scripts
- **package.json** - JavaScript dependencies
- **.env.example** - Environment configuration template
- **phpunit.xml** - Test configuration
- **vite.config.js** - Frontend build configuration

### Utility Files
- **export_songs.php** - Song data export utility
- **test_admin_controller.php** - Controller testing utility
- **songs.csv** - Sample song data

## API Response Formats

### Success Responses
All successful API calls return JSON with appropriate HTTP status codes:
- `200 OK` - Successful GET, PUT, PATCH requests
- `201 Created` - Successful POST requests
- `204 No Content` - Successful DELETE requests

### Error Responses
```json
{
  "error": "Error type",
  "message": "Detailed error message"
}
```

### Pagination Format (Laravel Standard)
```json
{
  "current_page": 1,
  "data": [...],
  "first_page_url": "...",
  "from": 1,
  "last_page": 5,
  "last_page_url": "...",
  "links": [...],
  "next_page_url": "...",
  "path": "...",
  "per_page": 15,
  "prev_page_url": null,
  "to": 15,
  "total": 75
}
```

## Future Enhancements

Potential areas for expansion:
1. **Audio Management** - Upload and serve audio files for songs
2. **PDF Generation** - Generate printable song sheets
3. **Favorites System** - Allow users to save favorite songs
4. **Playlists** - Create and manage song collections
5. **Comments/Reviews** - User feedback on songs
6. **Advanced Permissions** - Granular permission system for admins
7. **Audit Logs** - Track all admin actions
8. **Multi-tenancy** - Support multiple church organizations
9. **Offline Support** - PWA capabilities for offline access
10. **Analytics** - Track song popularity and usage patterns

## Contributing

When contributing to this project:
1. Follow Laravel coding standards (enforced by Laravel Pint)
2. Write tests for new features
3. Update relevant documentation
4. Use meaningful commit messages
5. Ensure all tests pass before submitting PRs

## License

This project is built on Laravel, which is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

**Project Repository**: [kamarlaylatt/calvary-songs-book-api](https://github.com/kamarlaylatt/calvary-songs-book-api)

For detailed API usage examples, please refer to:
- [Admin API Documentation](ADMIN_API_DOCUMENTATION.MD)
- [User API Documentation](USER_API_DOCUMENTATION.MD)
