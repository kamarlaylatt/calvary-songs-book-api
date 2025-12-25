# User API Documentation

This documentation provides details about the available API endpoints for users.

## Authentication

### Login

-   **Endpoint:** `POST /api/login`
-   **Description:** Authenticates a user and returns an API token.
-   **Request Body:**
    ```json
    {
        "email": "user@example.com",
        "password": "password"
    }
    ```
-   **Response:**
    ```json
    {
        "message": "Login successful",
        "user": {
            "id": 1,
            "name": "User Name",
            "email": "user@example.com",
            "created_at": "2025-07-17T05:50:20.000000Z",
            "updated_at": "2025-07-17T05:50:20.000000Z"
        },
        "token": "your-api-token"
    }
    ```

### Logout

-   **Endpoint:** `POST /api/logout`
-   **Description:** Logs out the authenticated user.
-   **Authentication:** Bearer Token
-   **Response:**
    ```json
    {
        "message": "Logout successful"
    }
    ```

### User Detail

-   **Endpoint:** `GET /api/user`
-   **Description:** Retrieves the details of the authenticated user.
-   **Authentication:** Bearer Token
-   **Response:**
    ```json
    {
        "user": {
            "id": 1,
            "name": "User Name",
            "email": "user@example.com",
            "created_at": "2025-07-17T05:50:20.000000Z",
            "updated_at": "2025-07-17T05:50:20.000000Z"
        }
    }
    ```

## Songs

### List Songs

-   **Endpoint:** `GET /api/songs`
-   **Description:** Retrieves a list of songs. Returns paginated response when 'limit' parameter is provided, otherwise returns all matching songs in a simple array.
-   **Query Parameters:**
    -   `search` (optional): A string to search for in the song title or lyrics. If a numeric value is provided, it will search by song ID.
    -   `style_id` (optional): Filter songs by style ID
    -   `category_id` (optional): Filter songs by category ID
    -   `song_language_id` (optional): Filter songs by Song Language ID. Accepts a single integer ID. Matches songs that have at least one related song language with that ID (many-to-many). To discover valid IDs, request `GET /api/search-filters` and use the "song_languages" list.
    -   `limit` (optional): Number of items per page (enables pagination)

#### Examples

-   Filter by language: `GET /api/songs?song_language_id=1`
-   Combine with category: `GET /api/songs?category_id=2&song_language_id=1`
-   Search by numeric ID and language: `GET /api/songs?search=10&song_language_id=1`
-   With pagination: `GET /api/songs?song_language_id=1&limit=15`

Note: When multiple filters are provided, they are combined with AND.

#### Paginated Response (when 'limit' parameter is provided):

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "code": 1,
            "title": "New Song Title",
            "slug": "new-song-title-1",
            "youtube": "https://www.youtube.com/watch?v=...",
            "description": "A description of the song.",
            "song_writer": "Songwriter Name",
            "style_id": 1,
            "lyrics": "The lyrics of the song.",
            "music_notes": "The music notes of the song.",
            "created_at": "2025-07-17T05:50:20.000000Z",
            "updated_at": "2025-07-17T05:50:20.000000Z",
            "style": {
                "id": 1,
                "name": "Style Name",
                "created_at": "2025-07-17T05:50:20.000000Z",
                "updated_at": "2025-07-17T05:50:20.000000Z"
            },
            "categories": [
                {
                    "id": 1,
                    "name": "Category Name",
                    "slug": "category-name",
                    "created_at": "2025-07-18T09:39:48.000000Z",
                    "updated_at": "2025-07-18T09:39:48.000000Z"
                }
            ],
            "song_languages": [
                {
                    "id": 1,
                    "name": "English",
                    "created_at": "2025-07-17T05:50:20.000000Z",
                    "updated_at": "2025-07-17T05:50:20.000000Z"
                }
            ]
        }
    ],
    "first_page_url": "http://localhost/api/songs?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost/api/songs?page=1",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "http://localhost/api/songs?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": null,
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": null,
    "path": "http://localhost/api/songs",
    "per_page": 15,
    "prev_page_url": null,
    "to": 1,
    "total": 1
}
```

#### Simple Array Response (when no 'limit' parameter):

```json
{
    "data": [
        {
            "id": 1,
            "code": 1,
            "title": "New Song Title",
            "slug": "new-song-title-1",
            "youtube": "https://www.youtube.com/watch?v=...",
            "description": "A description of the song.",
            "song_writer": "Songwriter Name",
            "style_id": 1,
            "lyrics": "The lyrics of the song.",
            "music_notes": "The music notes of the song.",
            "created_at": "2025-07-17T05:50:20.000000Z",
            "updated_at": "2025-07-17T05:50:20.000000Z",
            "style": {
                "id": 1,
                "name": "Style Name",
                "created_at": "2025-07-17T05:50:20.000000Z",
                "updated_at": "2025-07-17T05:50:20.000000Z"
            },
            "categories": [
                {
                    "id": 1,
                    "name": "Category Name",
                    "slug": "category-name",
                    "created_at": "2025-07-18T09:39:48.000000Z",
                    "updated_at": "2025-07-18T09:39:48.000000Z"
                }
            ],
            "song_languages": [
                {
                    "id": 1,
                    "name": "English",
                    "created_at": "2025-07-17T05:50:20.000000Z",
                    "updated_at": "2025-07-17T05:50:20.000000Z"
                }
            ]
        }
    ]
}
```

## Categories

### List Categories

-   **Endpoint:** `GET /api/categories`
-   **Description:** Retrieves a list of all categories.
-   **Response:** A JSON array of category objects:
    ```json
    [
        {
            "id": 1,
            "name": "Category Name",
            "slug": "category-name",
            "description": "Category description",
            "created_at": "2025-07-18T09:39:48.000000Z",
            "updated_at": "2025-07-18T09:39:48.000000Z"
        }
    ]
    ```

### Show Song

-   **Endpoint:** `GET /api/songs/{song:slug}`
-   **Description:** Retrieves a single song by its slug.
-   **Response:** The song object with full details:
    ```json
    {
        "id": 1,
        "code": 1,
        "title": "New Song Title",
        "slug": "new-song-title-1",
        "youtube": "https://www.youtube.com/watch?v=...",
        "description": "A description of the song.",
        "song_writer": "Songwriter Name",
        "style": {
            "id": 1,
            "name": "Style Name",
            "created_at": "2025-07-17T05:50:20.000000Z",
            "updated_at": "2025-07-17T05:50:20.000000Z"
        },
        "categories": [
            {
                "id": 1,
                "name": "Category Name",
                "slug": "category-name",
                "created_at": "2025-07-18T09:39:48.000000Z",
                "updated_at": "2025-07-18T09:39:48.000000Z"
            }
        ],
        "song_languages": [
            {
                "id": 1,
                "name": "English",
                "created_at": "2025-07-17T05:50:20.000000Z",
                "updated_at": "2025-07-17T05:50:20.000000Z"
            }
        ],
        "lyrics": "The lyrics of the song.",
        "music_notes": "The music notes of the song.",
        "created_at": "2025-07-17T05:50:20.000000Z",
        "updated_at": "2025-07-17T05:50:20.000000Z"
    }
    ```

## Search Filters

### Get Search Filters

-   **Endpoint:** `GET /api/search-filters`
-   **Description:** Retrieves categories, styles, and song languages for search screen filters.
-   **Response:** A JSON object containing categories, styles, and song languages:
    ```json
    {
        "categories": [
            {
                "id": 1,
                "name": "Category Name",
                "slug": "category-name"
            }
        ],
        "styles": [
            {
                "id": 1,
                "name": "Style Name"
            }
        ],
        "song_languages": [
            {
                "id": 1,
                "name": "English"
            }
        ]
    }
    ```

## Suggest Songs

### Submit Song Suggestion

-   **Endpoint:** `POST /api/suggest-songs`
-   **Description:** Allows users to suggest new songs from the mobile app. The suggested song will be pending approval by admin.
-   **Request Body:**
    ```json
    {
        "title": "Amazing Grace",
        "youtube": "https://youtube.com/watch?v=example",
        "description": "A beautiful hymn",
        "song_writer": "John Newton",
        "style_id": 1,
        "key": "G",
        "lyrics": "Amazing grace how sweet the sound\nThat saved a wretch like me",
        "music_notes": "Musical notation here",
        "popular_rating": 5,
        "email": "user@example.com",
        "category_ids": [1, 3],
        "song_language_ids": [1, 2]
    }
    ```
-   **Required Fields:**
    -   `title` (string): Song title (max 255 characters)
    -   `lyrics` (string): Song lyrics
-   **Optional Fields:**
    -   `youtube` (string): YouTube URL (max 255 characters)
    -   `description` (string): Song description
    -   `song_writer` (string): Name of the song writer (max 255 characters)
    -   `style_id` (integer): ID of the song style (must exist in styles table)
    -   `key` (string): Musical key (max 255 characters)
    -   `music_notes` (string): Musical notation or notes
    -   `popular_rating` (integer): Rating from 0-5
    -   `email` (string): Email address for approval notifications (optional)
    -   `category_ids` (array): Array of category IDs to associate with the suggested song (optional, each ID must exist in categories table)
    -   `song_language_ids` (array): Array of song language IDs to associate with the suggested song (optional, each ID must exist in song_languages table)
-   **Success Response (201):**
    ```json
    {
        "message": "Song suggestion submitted successfully",
        "data": {
            "id": 1,
            "title": "Amazing Grace",
            "youtube": "https://youtube.com/watch?v=example",
            "description": "A beautiful hymn",
            "song_writer": "John Newton",
            "style_id": 1,
            "key": "G",
            "lyrics": "Amazing grace how sweet the sound\nThat saved a wretch like me",
            "music_notes": "Musical notation here",
            "popular_rating": 5,
            "status": 1,
            "categories": [
                {
                    "id": 1,
                    "name": "Category Name",
                    "pivot": {
                        "suggest_song_id": 1,
                        "category_id": 1
                    }
                }
            ],
            "created_at": "2025-12-23T15:20:00.000000Z",
            "updated_at": "2025-12-23T15:20:00.000000Z"
        }
    }
    ```
-   **Validation Error Response (422):**
    ```json
    {
        "message": "The title field is required. (and 1 more error)",
        "errors": {
            "title": ["The title field is required."],
            "lyrics": ["The lyrics field is required."]
        }
    }
    ```
-   **Status Field Values:**
    -   `0`: Cancelled
    -   `1`: Pending (default)
    -   `2`: Approved
-   **Notes:**
    -   The slug is automatically generated from the title
    -   Duplicate slugs are handled with numeric suffixes (e.g., "amazing-grace-1")
    -   The status is automatically set to `1` (pending) upon submission
    -   Admin will review the suggestion and send an email notification about the approval status

## Version Check (Force Update)

### Check App Version

-   **Endpoint:** `POST /api/check-version`
-   **Description:** Checks if the Android/iOS app needs to be updated by comparing the current version with the minimum required version.
-   **Request Body:**
    ```json
    {
        "version_code": 1,
        "platform": "android"
    }
    ```
-   **Parameters:**
    -   `version_code` (required): Integer representing the current app version code
    -   `platform` (required): String, must be either "android" or "ios"
-   **Success Response:**
    ```json
    {
        "needs_update": true,
        "current_version_code": 1,
        "minimum_version_code": 3,
        "latest_version_code": 4,
        "latest_version_name": "1.3.0",
        "update_url": "https://play.google.com/store/apps/details?id=com.example.calvarysongs",
        "release_notes": "Latest version with new features",
        "message": "A new version is available. Please update to continue using the app."
    }
    ```
-   **Up to Date Response:**
    ```json
    {
        "needs_update": false,
        "current_version_code": 4,
        "minimum_version_code": 3,
        "latest_version_code": 4,
        "latest_version_name": "1.3.0",
        "update_url": "https://play.google.com/store/apps/details?id=com.example.calvarysongs",
        "release_notes": "Latest version with new features",
        "message": "Your app is up to date."
    }
    ```
-   **Validation Error Response (422):**
    ```json
    {
        "error": "Validation failed",
        "message": "The platform field is required."
    }
    ```
-   **Notes:**
    -   The API compares the provided `version_code` with the minimum force update version stored in the database
    -   If `needs_update` is `true`, the app should prompt the user to update using the provided `update_url`
    -   The `update_url` points to the Play Store (Android) or App Store (iOS) listing
    -   Version information is managed through the `app_versions` database table
