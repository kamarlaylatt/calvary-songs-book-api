# Calvary Songs Book API

A Laravel-based REST API for managing a songs book system with both admin and user functionality.

## Features

- User authentication and authorization
- Songs management with categories, styles, and languages
- Song search and filtering
- Song suggestions from mobile app users
- Version control for force updates
- Admin panel for managing songs, categories, styles, and users

## Documentation

- **[User API Documentation](USER_API_DOCUMENTATION.MD)** - Complete documentation for user-facing API endpoints
- **[Admin API Documentation](ADMIN_API_DOCUMENTATION.MD)** - Complete documentation for admin API endpoints
- **[Postman Collection](Calvary_Songs_Book_API.postman_collection.json)** - Ready-to-use Postman collection for testing
- **[Postman Collection Guide](POSTMAN_COLLECTION_README.md)** - Instructions for using the Postman collection

## Quick Start

### Installation

1. Clone the repository
   ```bash
   git clone https://github.com/kamarlaylatt/calvary-songs-book-api.git
   cd calvary-songs-book-api
   ```

2. Install dependencies
   ```bash
   composer install
   npm install
   ```

3. Set up environment
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure database in `.env` file

5. Run migrations
   ```bash
   php artisan migrate
   ```

6. Start development server
   ```bash
   composer run dev
   ```

### Testing

Run the test suite:
```bash
composer test
# or
php artisan test
```

## API Endpoints Overview

### Public Endpoints
- `POST /api/login` - User authentication
- `GET /api/songs` - List songs with filters
- `GET /api/songs/{slug}` - Get single song
- `GET /api/categories` - List categories
- `GET /api/search-filters` - Get search filters
- `POST /api/suggest-songs` - Submit song suggestion
- `POST /api/check-version` - Check app version

### Protected Endpoints
- `POST /api/logout` - Logout user
- `GET /api/user` - Get user details

## Development Commands

- `composer run dev` - Start development server with all services
- `composer test` - Run tests
- `npm run dev` - Start Vite dev server
- `npm run build` - Build assets for production

## Tech Stack

- **Framework**: Laravel 12.0
- **PHP**: 8.2+
- **Database**: SQLite (can be configured for MySQL/PostgreSQL)
- **Frontend Build**: Vite
- **CSS**: TailwindCSS 4.0
- **Testing**: PHPUnit

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

