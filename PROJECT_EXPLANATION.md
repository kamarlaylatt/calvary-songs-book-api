# Calvary Songs Book API - Project Explanation

## 📖 Overview

**Calvary Songs Book API** is a comprehensive Laravel-based REST API designed to manage a digital songs book system, specifically for the Calvary church community. This API powers mobile applications (Android/iOS) and provides both administrative and user-facing functionality for browsing, managing, and organizing worship songs.

## 🎯 Purpose

The primary purpose of this API is to:

1. **Centralized Song Management**: Provide a single source of truth for worship songs, including lyrics, music notes, and metadata
2. **Multi-language Support**: Support songs in multiple languages to serve diverse congregations
3. **Categorization & Discovery**: Enable easy song discovery through categories, styles, and search functionality
4. **Administration**: Allow church administrators to manage songs, categories, languages, and other content
5. **Mobile App Support**: Provide REST endpoints for mobile applications with features like version checking and force updates
6. **User Authentication**: Support both admin and regular user authentication with role-based access control

## 🏗️ Architecture

### Technology Stack

- **Framework**: Laravel 12.0
- **PHP Version**: 8.2+
- **Database**: SQLite (with easy migration to MySQL/PostgreSQL)
- **Authentication**: Laravel Sanctum (API token-based)
- **Frontend Assets**: Vite + TailwindCSS 4.0
- **Testing**: PHPUnit
- **Code Quality**: Laravel Pint

### Key Components

#### 1. **Models & Database Schema**

The API manages the following key entities:

- **Songs**: Core entity containing title, lyrics, music notes, YouTube links, and metadata
  - Fields: id, code, slug, title, description, lyrics, music_notes, youtube, song_writer, popular_rating
  - Relationships: belongs to Style, many-to-many with Categories and Song Languages
  
- **Categories**: Organizational groups for songs (e.g., "Worship", "Prayer", "Praise")
  - Fields: id, name, slug, description, sort_no
  - Sortable via sort_no for custom ordering
  
- **Styles**: Musical styles or genres (e.g., "Contemporary", "Traditional")
  - Fields: id, name
  
- **Song Languages**: Languages in which songs are available
  - Fields: id, name
  - Many-to-many relationship with songs
  
- **Admins**: Administrative users with role-based access
  - Fields: id, name, email, password, status
  - Relationships: many-to-many with Roles
  - Supports soft deletes
  
- **Roles**: Admin roles (e.g., "Super Admin", "Editor")
  - Fields: id, name
  
- **Users**: Regular users who consume the API
  - Fields: id, name, email, password
  
- **App Versions**: Version control for mobile apps
  - Fields: id, platform (android/ios), version_code, version_name, minimum_version_code, update_url, release_notes

#### 2. **API Endpoints**

The API is organized into two main sections:

**Admin API** (`/api/admin/*`) - Requires authentication:
- Authentication (login, logout, profile)
- CRUD operations for:
  - Songs (with advanced filtering, sorting, and search)
  - Categories (with custom sorting)
  - Styles
  - Song Languages
  - Admins (user management)
  - Roles

**User API** (`/api/*`) - Public and authenticated endpoints:
- Authentication (login, logout, profile)
- Song browsing and search (with filtering by category, style, language)
- Category listing
- Search filters (for building search UIs)
- Version checking (for force update functionality)

#### 3. **Features**

##### Search & Filtering
- **Full-text search** on song titles and lyrics
- **Numeric search** by song ID or code
- **Filter by**:
  - Category
  - Style
  - Language
  - Popular rating
- **Sorting** by creation date, ID, or other fields
- **Pagination** support with configurable page sizes

##### Admin Features
- Role-based access control
- Complete CRUD operations
- Soft deletes for admins
- Status management (active/inactive)
- Bulk operations support

##### Mobile App Support
- **Version Control**: Force update mechanism to ensure users have compatible app versions
- **Platform-specific**: Separate version tracking for Android and iOS
- **Update URLs**: Direct links to app stores for updates
- **Release Notes**: Informative messages about new versions

##### Data Organization
- **Slugs**: Auto-generated URL-friendly slugs for songs and categories
- **Codes**: Unique numeric codes for songs
- **Relationships**: Proper many-to-many relationships for complex associations
- **Eager Loading**: Optimized queries to prevent N+1 problems

## 🚀 Getting Started

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & npm
- SQLite (or MySQL/PostgreSQL)

### Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/kamarlaylatt/calvary-songs-book-api.git
   cd calvary-songs-book-api
   ```

2. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Create database**:
   ```bash
   touch database/database.sqlite
   ```

5. **Run migrations**:
   ```bash
   php artisan migrate
   ```

6. **Seed database** (optional):
   ```bash
   php artisan db:seed
   ```

7. **Start development server**:
   ```bash
   composer run dev
   ```
   This starts multiple processes concurrently:
   - Laravel development server
   - Queue worker
   - Log viewer (Pail)
   - Vite dev server

### Testing

```bash
composer test
# or
php artisan test
```

### Code Formatting

```bash
vendor/bin/pint
```

## 📚 Documentation

Comprehensive API documentation is available:

- **[ADMIN_API_DOCUMENTATION.MD](ADMIN_API_DOCUMENTATION.MD)**: Complete admin API reference with request/response examples
- **[USER_API_DOCUMENTATION.MD](USER_API_DOCUMENTATION.MD)**: User-facing API endpoints documentation
- **[CLAUDE.md](CLAUDE.md)**: Development guidelines and project-specific instructions for AI assistants

## 🔐 Authentication

The API uses **Laravel Sanctum** for token-based authentication:

1. **Login**: POST to `/api/login` or `/api/admin/login` with credentials
2. **Receive Token**: API returns a bearer token
3. **Authenticate Requests**: Include token in `Authorization: Bearer {token}` header
4. **Logout**: POST to `/api/logout` or `/api/admin/logout` to revoke token

## 📊 Use Cases

### For Church Administrators
1. **Content Management**: Add new songs with lyrics, music notes, and YouTube videos
2. **Organization**: Categorize songs for easy discovery (e.g., "Sunday Service", "Youth Worship")
3. **Multi-language Support**: Tag songs with languages to serve bilingual congregations
4. **User Management**: Create and manage admin accounts with different permission levels
5. **App Updates**: Control mobile app versions and force updates when necessary

### For Church Members (Users)
1. **Browse Songs**: Explore the complete song catalog with search and filters
2. **Find by Category**: Quickly find songs for specific occasions or themes
3. **Search**: Find songs by title, lyrics, or song number
4. **Language Filter**: View songs in their preferred language
5. **Detailed View**: Access complete lyrics, music notes, and YouTube videos

### For Mobile App Developers
1. **API Integration**: RESTful endpoints with consistent JSON responses
2. **Authentication**: Token-based auth for secure API access
3. **Pagination**: Efficient data loading with paginated responses
4. **Version Control**: Automatic version checking and force update mechanism
5. **Search Filters**: Pre-built filter endpoint for building search UIs

## 🌟 Key Features Breakdown

### 1. Smart Search
- Full-text search across titles and lyrics
- Numeric search by song code
- Combined filters (category + language + style)
- Instant results with pagination

### 2. Flexible Response Formats
- **Paginated**: When `limit` parameter is provided
- **Full List**: When no pagination is requested
- Consistent structure across all endpoints

### 3. Version Management
The API includes sophisticated version control for mobile apps:
- Platform-specific versioning (Android/iOS)
- Minimum version enforcement (force updates)
- Latest version tracking
- Update URLs and release notes
- Graceful handling of version checks

### 4. Admin Dashboard Support
Complete backend for building admin dashboards:
- User management with roles
- Content CRUD operations
- Search and filtering
- Bulk operations support
- Status management

### 5. Data Integrity
- Input validation using Form Requests
- Relationship constraints (foreign keys)
- Soft deletes for admins
- Auto-generated slugs and codes
- Status tracking

## 🔄 Data Flow Example

### Adding a New Song (Admin Flow)
```
1. Admin logs in → Receives bearer token
2. Admin POSTs to /api/admin/songs with:
   - title: "Amazing Grace"
   - lyrics: "..."
   - style_id: 1 (Traditional)
   - category_ids: [1, 3] (Worship, Classic)
   - song_language_ids: [1] (English)
3. API generates:
   - slug: "amazing-grace-123"
   - code: 123 (auto-incremented)
4. Song is saved with relationships
5. API returns complete song object with relationships
```

### Searching for a Song (User Flow)
```
1. User (optionally) logs in
2. User GETs /api/songs?search=grace&category_id=1&song_language_id=1
3. API:
   - Performs full-text search on "grace"
   - Filters by category_id=1
   - Filters by song_language_id=1
   - Returns matching songs with eager-loaded relationships
4. User receives array of songs with categories, styles, and languages
```

### Force Update Check (Mobile App)
```
1. App POSTs to /api/check-version with:
   - version_code: 2
   - platform: "android"
2. API checks app_versions table:
   - minimum_version_code: 3
   - latest_version_code: 4
3. API responds:
   - needs_update: true
   - update_url: "https://play.google.com/..."
   - release_notes: "New features and bug fixes"
4. App displays update prompt
```

## 🧪 Testing Strategy

The project uses PHPUnit with in-memory SQLite for fast testing:

- **Feature Tests**: Test complete API flows (routes/api.php)
- **Unit Tests**: Test individual components
- **Test Database**: In-memory SQLite for isolation
- **Factories**: Generate test data easily
- **Assertions**: Validate JSON responses, status codes, and data integrity

## 📈 Future Enhancements

Potential areas for expansion:
1. **Playlists**: Allow users to create song collections
2. **Favorites**: User-specific favorite songs
3. **Sheet Music**: PDF uploads for printed music
4. **Audio Files**: MP3/audio file support
5. **Notifications**: Push notifications for new songs
6. **Analytics**: Track popular songs and usage patterns
7. **Comments/Ratings**: Community feedback on songs
8. **Export**: Generate PDF songbooks or chord sheets

## 🤝 Contributing

When contributing to this repository:
1. Follow Laravel conventions and best practices
2. Use Laravel Pint for code formatting (`vendor/bin/pint`)
3. Write tests for new features
4. Update API documentation when adding endpoints
5. Maintain backward compatibility

## 📞 Support & Contact

For questions or support related to this API:
- Check the API documentation files (ADMIN_API_DOCUMENTATION.MD, USER_API_DOCUMENTATION.MD)
- Review the Laravel 12 documentation: https://laravel.com/docs
- Contact the repository maintainer: [GitHub Profile](https://github.com/kamarlaylatt)

## 📝 License

This project is open-sourced software licensed under the MIT license (same as Laravel framework).

---

**Built with ❤️ using Laravel** - Making worship song management simple and accessible for churches worldwide.
