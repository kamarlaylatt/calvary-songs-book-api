# Calvary Songs Book API

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

<p align="center">
  <strong>A comprehensive REST API for managing worship songs for churches</strong>
</p>

## 📖 About This Project

**Calvary Songs Book API** is a Laravel-based REST API designed to power a digital songs book system for churches. It provides robust endpoints for managing worship songs, categories, styles, languages, and administrative functions. The API supports mobile applications (Android/iOS) with features like authentication, advanced search, and version control.

### Key Features

- 🎵 **Song Management**: Complete CRUD operations for songs with lyrics, music notes, and YouTube links
- 🔍 **Advanced Search**: Full-text search with filtering by category, style, language, and rating
- 🌍 **Multi-language Support**: Songs can be tagged with multiple languages
- 📱 **Mobile App Support**: Version checking and force update mechanism
- 🔐 **Authentication**: Sanctum-based token authentication with role-based access control
- 👥 **Admin Panel**: Comprehensive admin endpoints for content management
- 📊 **Organized Content**: Categories, styles, and custom sorting capabilities

## 🚀 Quick Start

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & npm
- SQLite (or MySQL/PostgreSQL)

### Installation

```bash
# Clone the repository
git clone https://github.com/kamarlaylatt/calvary-songs-book-api.git
cd calvary-songs-book-api

# Install dependencies
composer install
npm install

# Set up environment
cp .env.example .env
php artisan key:generate

# Create database and run migrations
touch database/database.sqlite
php artisan migrate

# Start the development server
composer run dev
```

Visit `http://localhost:8000` to access the API.

### Running Tests

```bash
composer test
```

### Code Formatting

```bash
vendor/bin/pint
```

## 📚 Documentation

- **[PROJECT_EXPLANATION.md](PROJECT_EXPLANATION.md)** - Comprehensive project overview, architecture, and detailed explanation
- **[ADMIN_API_DOCUMENTATION.MD](ADMIN_API_DOCUMENTATION.MD)** - Complete admin API reference
- **[USER_API_DOCUMENTATION.MD](USER_API_DOCUMENTATION.MD)** - User-facing API endpoints
- **[CLAUDE.md](CLAUDE.md)** - Development guidelines and AI assistant instructions

## 🏗️ Technology Stack

- **Framework**: Laravel 12.0
- **Authentication**: Laravel Sanctum
- **Database**: SQLite (production-ready for MySQL/PostgreSQL)
- **Frontend Build**: Vite + TailwindCSS 4.0
- **Testing**: PHPUnit with SQLite in-memory database
- **Code Quality**: Laravel Pint

## 📊 API Overview

### Admin Endpoints (`/api/admin/*`)

- Authentication (login, logout, profile)
- Songs CRUD with advanced filtering
- Categories management with custom sorting
- Styles and Song Languages management
- Admin users and roles management

### User Endpoints (`/api/*`)

- Authentication (login, logout, profile)
- Browse songs with search and filters
- View song details by slug
- Get search filters
- Check app version (force update)

## 🎯 Use Cases

- **For Church Administrators**: Manage song library, organize content, control user access
- **For Church Members**: Browse and search songs, view lyrics and music notes
- **For Mobile Developers**: Integrate REST API with iOS/Android apps, implement version control

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. Follow Laravel conventions and best practices
2. Use Laravel Pint for code formatting
3. Write tests for new features
4. Update documentation when adding endpoints
5. Maintain backward compatibility

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🔗 Related Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [API Design Best Practices](https://laravel.com/docs/eloquent-resources)

---

**Built with ❤️ using Laravel** - Powered by [Laravel 12](https://laravel.com)
