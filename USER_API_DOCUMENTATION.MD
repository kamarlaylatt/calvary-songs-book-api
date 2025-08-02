# User API Documentation

This documentation provides details about the available API endpoints for users.

## Authentication

### Login

- **Endpoint:** `POST /api/login`
- **Description:** Authenticates a user and returns an API token.
- **Request Body:**
  ```json
  {
    "email": "user@example.com",
    "password": "password"
  }
  ```
- **Response:**
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

- **Endpoint:** `POST /api/logout`
- **Description:** Logs out the authenticated user.
- **Authentication:** Bearer Token
- **Response:**
  ```json
  {
    "message": "Logout successful"
  }
  ```

### User Detail

- **Endpoint:** `GET /api/user`
- **Description:** Retrieves the details of the authenticated user.
- **Authentication:** Bearer Token
- **Response:**
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

- **Endpoint:** `GET /api/songs`
- **Description:** Retrieves a list of songs. Returns paginated response when 'limit' parameter is provided, otherwise returns all matching songs in a simple array.
- **Query Parameters:**
  - `search` (optional): A string to search for in the song title or lyrics
  - `style_id` (optional): Filter songs by style ID
  - `category_id` (optional): Filter songs by category ID
  - `limit` (optional): Number of items per page (enables pagination)
  
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
      ]
    }
  ]
}
```

## Categories

### List Categories

- **Endpoint:** `GET /api/categories`
- **Description:** Retrieves a list of all categories.
- **Response:** A JSON array of category objects:
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

- **Endpoint:** `GET /api/songs/{song:slug}`
- **Description:** Retrieves a single song by its slug.
- **Response:** The song object with full details:
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
    "lyrics": "The lyrics of the song.",
    "music_notes": "The music notes of the song.",
    "created_at": "2025-07-17T05:50:20.000000Z",
    "updated_at": "2025-07-17T05:50:20.000000Z"
  }

## Search Filters

### Get Search Filters
- **Endpoint:** `GET /api/search-filters`
- **Description:** Retrieves all categories and styles for search screen filters.
- **Response:** A JSON object containing both categories and styles:
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
    ]
  }
  ```