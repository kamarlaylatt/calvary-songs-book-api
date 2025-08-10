# Admin API Documentation

This documentation provides details about the available API endpoints for administrators.

## Authentication

### Login

- **Endpoint:** `POST /api/admin/login`
- **Description:** Authenticates an admin user and returns an API token.
- **Request Body:**
  ```json
  {
    "email": "admin@example.com",
    "password": "password"
  }
  ```
- **Response:**
  ```json
  {
    "message": "Login successful",
    "admin": {
      "id": 1,
      "name": "Admin Name",
      "email": "admin@example.com",
      "created_at": "2025-07-17T05:50:20.000000Z",
      "updated_at": "2025-07-17T05:50:20.000000Z"
    },
    "token": "your-api-token"
  }
  ```

### Logout

- **Endpoint:** `POST /api/admin/logout`
- **Description:** Logs out the authenticated admin user.
- **Authentication:** Bearer Token
- **Response:**
  ```json
  {
    "message": "Logout successful"
  }
  ```

### Admin Detail

- **Endpoint:** `GET /api/admin`
- **Description:** Retrieves the details of the authenticated admin user.
- **Authentication:** Bearer Token
- **Response:**
  ```json
  {
    "admin": {
      "id": 1,
      "name": "Admin Name",
      "email": "admin@example.com",
      "created_at": "2025-07-17T05:50:20.000000Z",
      "updated_at": "2025-07-17T05:50:20.000000Z"
    }
  }
  ```

## Songs

### List Songs

- **Endpoint:** `GET /api/admin/songs`
- **Description:** Retrieves a paginated list of songs.
- **Authentication:** Bearer Token
- **Query Parameters:**
  - `search` (optional): A string to search for in the song title, songwriter, or lyrics.
- **Response:** A paginated JSON object with the following structure:
  ```json
  {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "admin_id": 1,
        "style_id": 1,
        "code": 1,
        "slug": "new-song-title-1",
        "title": "New Song Title",
        "youtube": "https://www.youtube.com/watch?v=...",
        "description": "A description of the song.",
        "song_writer": "Songwriter Name",
        "lyrics": "The lyrics of the song.",
        "music_notes": "The music notes of the song.",
        "popular_rating": 3,
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
            "name": "Category 1",
            "slug": "category-1",
            "description": "Category description",
            "created_at": "2025-07-18T09:39:48.000000Z",
            "updated_at": "2025-07-18T09:39:48.000000Z"
          }
        ]
      }
    ],
    "first_page_url": "http://localhost/api/admin/songs?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost/api/admin/songs?page=1",
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://localhost/api/admin/songs?page=1",
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
    "path": "http://localhost/api/admin/songs",
    "per_page": 15,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
  ```

### Create Song

- **Endpoint:** `POST /api/admin/songs`
- **Description:** Creates a new song.
- **Authentication:** Bearer Token
- **Request Body:**
  ```json
  {
    "title": "New Song Title",
    "youtube": "https://www.youtube.com/watch?v=...",
    "description": "A description of the song.",
    "song_writer": "Songwriter Name",
    "style_id": 1, // nullable, must exist in styles table if present
    "lyrics": "The lyrics of the song.",
    "music_notes": "The music notes of the song.",
    "popular_rating": 3,
    "category_ids": [1, 2]
  }
  ```
- **Response:** The newly created song object with auto-generated fields:
  ```json
  {
    "id": 1,
    "admin_id": 1,
    "style_id": 1,
    "code": 1,
    "slug": "new-song-title-1",
    "title": "New Song Title",
    "youtube": "https://www.youtube.com/watch?v=...",
    "description": "A description of the song.",
    "song_writer": "Songwriter Name",
    "lyrics": "The lyrics of the song.",
    "music_notes": "The music notes of the song.",
    "popular_rating": 3,
    "created_at": "2025-07-17T05:50:20.000000Z",
    "updated_at": "2025-07-17T05:50:20.000000Z",
    "categories": [
      {
        "id": 1,
        "name": "Category 1",
        "slug": "category-1",
        "description": "Category description",
        "created_at": "2025-07-18T09:39:48.000000Z",
        "updated_at": "2025-07-18T09:39:48.000000Z"
      },
      {
        "id": 2,
        "name": "Category 2",
        "slug": "category-2",
        "description": "Category description",
        "created_at": "2025-07-18T09:39:48.000000Z",
        "updated_at": "2025-07-18T09:39:48.000000Z"
      }
    ]
  }
  ```

### Show Song

- **Endpoint:** `GET /api/admin/songs/{song}`
- **Description:** Retrieves a single song by its ID.
- **Authentication:** Bearer Token
- **Response:** The song object with related style:
  ```json
  {
    "id": 1,
    "admin_id": 1,
    "style_id": 1,
    "code": 1,
    "slug": "new-song-title-1",
    "title": "New Song Title",
    "youtube": "https://www.youtube.com/watch?v=...",
    "description": "A description of the song.",
    "song_writer": "Songwriter Name",
    "lyrics": "The lyrics of the song.",
    "music_notes": "The music notes of the song.",
    "popular_rating": 3,
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
        "name": "Category 1",
        "slug": "category-1",
        "description": "Category description",
        "created_at": "2025-07-18T09:39:48.000000Z",
        "updated_at": "2025-07-18T09:39:48.000000Z"
      }
    ]
  }
  ```

### Update Song

- **Endpoint:** `PUT/PATCH /api/admin/songs/{song}`
- **Description:** Updates an existing song.
- **Authentication:** Bearer Token
- **Request Body:** (same as create, but all fields are optional; `style_id` is nullable and, if present, must exist in styles table)
- **Response:** The updated song object:
  ```json
  {
    "id": 1,
    "admin_id": 1,
    "style_id": 1,
    "code": 1,
    "slug": "new-song-title-1",
    "title": "Updated Song Title",
    "youtube": "https://www.youtube.com/watch?v=...",
    "description": "Updated description",
    "song_writer": "Updated Songwriter",
    "lyrics": "Updated lyrics",
    "music_notes": "Updated music notes",
    "popular_rating": 4,
    "created_at": "2025-07-17T05:50:20.000000Z",
    "updated_at": "2025-07-17T05:51:20.000000Z",
    "categories": [
      {
        "id": 1,
        "name": "Category 1",
        "slug": "category-1",
        "description": "Category description",
        "created_at": "2025-07-18T09:39:48.000000Z",
        "updated_at": "2025-07-18T09:39:48.000000Z"
      },
      {
        "id": 2,
        "name": "Category 2",
        "slug": "category-2",
        "description": "Category description",
        "created_at": "2025-07-18T09:39:48.000000Z",
        "updated_at": "2025-07-18T09:39:48.000000Z"
      },
      {
        "id": 3,
        "name": "Category 3",
        "slug": "category-3",
        "description": "Category description",
        "created_at": "2025-07-18T09:39:48.000000Z",
        "updated_at": "2025-07-18T09:39:48.000000Z"
      }
    ]
  }
  ```

### Delete Song

- **Endpoint:** `DELETE /api/admin/songs/{song}`
- **Description:** Deletes a song.
- **Authentication:** Bearer Token
- **Response:** `204 No Content`

## Styles

### List Styles

- **Endpoint:** `GET /api/admin/styles`
- **Description:** Retrieves a list of all styles.
- **Authentication:** Bearer Token
- **Response:** A JSON array of style objects:
  ```json
  [
    {
      "id": 1,
      "name": "Style 1",
      "created_at": "2025-07-17T05:50:20.000000Z",
      "updated_at": "2025-07-17T05:50:20.000000Z"
    },
    {
      "id": 2,
      "name": "Style 2",
      "created_at": "2025-07-17T05:50:20.000000Z",
      "updated_at": "2025-07-17T05:50:20.000000Z"
    }
  ]
  ```

### Create Style

- **Endpoint:** `POST /api/admin/styles`
- **Description:** Creates a new style.
- **Authentication:** Bearer Token
- **Request Body:**
  ```json
  {
    "name": "New Style Name"
  }
  ```
- **Response:** The newly created style object:
  ```json
  {
    "id": 3,
    "name": "New Style Name",
    "created_at": "2025-07-17T05:50:20.000000Z",
    "updated_at": "2025-07-17T05:50:20.000000Z"
  }
  ```

### Show Style

- **Endpoint:** `GET /api/admin/styles/{style}`
- **Description:** Retrieves a single style by its ID.
- **Authentication:** Bearer Token
- **Response:** The style object:
  ```json
  {
    "id": 1,
    "name": "Style Name",
    "created_at": "2025-07-17T05:50:20.000000Z",
    "updated_at": "2025-07-17T05:50:20.000000Z"
  }
  ```

### Update Style

- **Endpoint:** `PUT/PATCH /api/admin/styles/{style}`
- **Description:** Updates an existing style.
- **Authentication:** Bearer Token
- **Request Body:**
  ```json
  {
    "name": "Updated Style Name"
  }
  ```
- **Response:** The updated style object:
  ```json
  {
    "id": 1,
    "name": "Updated Style Name",
    "created_at": "2025-07-17T05:50:20.000000Z",
    "updated_at": "2025-07-17T05:51:20.000000Z"
  }
  ```

### Delete Style

- **Endpoint:** `DELETE /api/admin/styles/{style}`
- **Description:** Deletes a style.
- **Authentication:** Bearer Token
- **Response:** `204 No Content`

## Categories

### List Categories

- **Endpoint:** `GET /api/admin/categories`
- **Description:** Retrieves a paginated list of categories.
- **Authentication:** Bearer Token
- **Response:** A paginated JSON object with category data:
  ```json
  {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Category Name",
        "slug": "category-name",
        "description": "Category description",
        "created_at": "2025-07-18T09:39:48.000000Z",
        "updated_at": "2025-07-18T09:39:48.000000Z"
      }
    ],
    "first_page_url": "http://localhost/api/admin/categories?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost/api/admin/categories?page=1",
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://localhost/api/admin/categories?page=1",
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
    "path": "http://localhost/api/admin/categories",
    "per_page": 10,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
  ```

### Create Category

- **Endpoint:** `POST /api/admin/categories`
- **Description:** Creates a new category.
- **Authentication:** Bearer Token
- **Request Body:**
  ```json
  {
    "name": "New Category Name",
    "description": "Optional category description"
  }
  ```
- **Response:** The newly created category object:
  ```json
  {
    "id": 1,
    "name": "New Category Name",
    "slug": "new-category-name",
    "description": "Optional category description",
    "created_at": "2025-07-18T09:39:48.000000Z",
    "updated_at": "2025-07-18T09:39:48.000000Z"
  }
  ```

### Show Category

- **Endpoint:** `GET /api/admin/categories/{category}`
- **Description:** Retrieves a single category by its ID.
- **Authentication:** Bearer Token
- **Response:** The category object:
  ```json
  {
    "id": 1,
    "name": "Category Name",
    "slug": "category-name",
    "description": "Category description",
    "created_at": "2025-07-18T09:39:48.000000Z",
    "updated_at": "2025-07-18T09:39:48.000000Z"
  }
  ```

### Update Category

- **Endpoint:** `PUT/PATCH /api/admin/categories/{category}`
- **Description:** Updates an existing category.
- **Authentication:** Bearer Token
- **Request Body:** (same as create, but fields are optional)
- **Response:** The updated category object:
  ```json
  {
    "id": 1,
    "name": "Updated Category Name",
    "slug": "updated-category-name",
    "description": "Updated description",
    "created_at": "2025-07-18T09:39:48.000000Z",
    "updated_at": "2025-07-18T09:40:48.000000Z"
  }
  ```

### Delete Category

- **Endpoint:** `DELETE /api/admin/categories/{category}`
- **Description:** Deletes a category.
- **Authentication:** Bearer Token
- **Response:** `204 No Content`
