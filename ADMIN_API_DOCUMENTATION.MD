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
      "updated_at": "2025-07-17T05:50:20.000000Z",
      "roles": [
        {
          "id": 1,
          "name": "Super Admin",
          "created_at": "2025-08-14T17:51:19.000000Z",
          "updated_at": "2025-08-14T17:51:19.000000Z"
        }
      ]
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
      "updated_at": "2025-07-17T05:50:20.000000Z",
      "roles": [
        {
          "id": 1,
          "name": "Super Admin",
          "created_at": "2025-08-14T17:51:19.000000Z",
          "updated_at": "2025-08-14T17:51:19.000000Z"
        }
      ]
    }
  }
  ```

## Admins

### List Admins

- **Endpoint:** `GET /api/admin/admins`
- **Description:** Retrieves a paginated list of admins.
- **Authentication:** Bearer Token
- **Response:** A paginated JSON object with the following structure:
  ```json
  {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Admin Name",
        "email": "admin@example.com",
        "status": "active",
        "created_at": "2025-07-17T05:50:20.000000Z",
        "updated_at": "2025-07-17T05:50:20.000000Z",
        "roles": [
          {
            "id": 1,
            "name": "Super Admin",
            "created_at": "2025-08-14T17:51:19.000000Z",
            "updated_at": "2025-08-14T17:51:19.000000Z"
          }
        ]
      }
    ],
    "first_page_url": "http://localhost/api/admin/admins?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost/api/admin/admins?page=1",
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://localhost/api/admin/admins?page=1",
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
    "path": "http://localhost/api/admin/admins",
    "per_page": 10,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
  ```

### Create Admin

- **Endpoint:** `POST /api/admin/admins`
- **Description:** Creates a new admin.
- **Authentication:** Bearer Token
- **Request Body:**
  ```json
  {
    "name": "New Admin Name",
    "email": "newadmin@example.com",
    "password": "password",
    "password_confirmation": "password",
    "status": "active",
    "roles": [1, 2]
  }
  ```
- **Response:** The newly created admin object:
  ```json
  {
    "id": 2,
    "name": "New Admin Name",
    "email": "newadmin@example.com",
    "status": "active",
    "created_at": "2025-08-14T18:30:00.000000Z",
    "updated_at": "2025-08-14T18:30:00.000000Z",
    "roles": [
      {
        "id": 1,
        "name": "Super Admin"
      },
      {
        "id": 2,
        "name": "Editor"
      }
    ]
  }
  ```

### Show Admin

- **Endpoint:** `GET /api/admin/admins/{admin}`
- **Description:** Retrieves a single admin by their ID.
- **Authentication:** Bearer Token
- **Response:** The admin object with related roles:
  ```json
  {
    "id": 1,
    "name": "Admin Name",
    "email": "admin@example.com",
    "status": "active",
    "created_at": "2025-07-17T05:50:20.000000Z",
    "updated_at": "2025-07-17T05:50:20.000000Z",
    "roles": [
      {
        "id": 1,
        "name": "Super Admin"
      }
    ]
  }
  ```

### Update Admin

- **Endpoint:** `PUT/PATCH /api/admin/admins/{admin}`
- **Description:** Updates an existing admin.
- **Authentication:** Bearer Token
- **Request Body:** (same as create, but all fields are optional)
- **Response:** The updated admin object:
  ```json
  {
    "id": 1,
    "name": "Updated Admin Name",
    "email": "admin@example.com",
    "status": "inactive",
    "created_at": "2025-07-17T05:50:20.000000Z",
    "updated_at": "2025-08-14T18:35:00.000000Z",
    "roles": [
      {
        "id": 2,
        "name": "Editor"
      }
    ]
  }
  ```

### Delete Admin

- **Endpoint:** `DELETE /api/admin/admins/{admin}`
- **Description:** Deletes an admin (soft delete).
- **Authentication:** Bearer Token
- **Response:** `204 No Content`

## Songs

### List Songs

- **Endpoint:** `GET /api/admin/songs`
- **Description:** Retrieves a paginated list of songs.
- **Authentication:** Bearer Token
- **Query Parameters:**
  - `id` (optional): Filter songs by specific ID.
  - `search` (optional): A string to search for in the song title or lyrics.
  - `style_id` (optional): Filter songs by style ID.
  - `category_ids` (optional): Filter songs by category IDs (array or single ID).
  - `song_language_ids` (optional): Filter songs by language IDs (array or single ID).
  - `sort_by` (optional): Field to sort by (supported: 'created_at', 'id').
  - `sort_order` (optional): Sort direction ('asc' or 'desc').
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
    "category_ids": [1, 2],
    "song_language_ids": [1, 2]
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
    ],
    "song_languages": [
      {
        "id": 1,
        "name": "English",
        "created_at": "2025-07-17T05:50:20.000000Z",
        "updated_at": "2025-07-17T05:50:20.000000Z"
      },
      {
        "id": 2,
        "name": "Spanish",
        "created_at": "2025-07-17T05:50:20.000000Z",
        "updated_at": "2025-07-17T05:50:20.000000Z"
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
- **Description:** Retrieves a paginated list of categories sorted by `sort_no` (ascending) and then by creation date (latest first).
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
        "sort_no": 1,
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
- **Description:** Creates a new category. The `sort_no` field controls the display order of categories (lower numbers appear first).
- **Authentication:** Bearer Token
- **Request Body:**
  ```json
  {
    "name": "New Category Name",
    "description": "Optional category description",
    "sort_no": 1
  }
  ```
- **Response:** The newly created category object:
  ```json
  {
    "id": 1,
    "name": "New Category Name",
    "slug": "new-category-name",
    "description": "Optional category description",
    "sort_no": 1,
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
    "sort_no": 1,
    "created_at": "2025-07-18T09:39:48.000000Z",
    "updated_at": "2025-07-18T09:39:48.000000Z"
  }
  ```

### Update Category

- **Endpoint:** `PUT/PATCH /api/admin/categories/{category}`
- **Description:** Updates an existing category. The `sort_no` field can be used to change the display order of categories.
- **Authentication:** Bearer Token
- **Request Body:** (same as create, but fields are optional)
  ```json
  {
    "name": "Updated Category Name",
    "description": "Updated description",
    "sort_no": 2
  }
  ```
- **Response:** The updated category object:
  ```json
  {
    "id": 1,
    "name": "Updated Category Name",
    "slug": "updated-category-name",
    "description": "Updated description",
    "sort_no": 2,
    "created_at": "2025-07-18T09:39:48.000000Z",
    "updated_at": "2025-07-18T09:40:48.000000Z"
  }
  ```

### Delete Category

- **Endpoint:** `DELETE /api/admin/categories/{category}`
- **Description:** Deletes a category.
- **Authentication:** Bearer Token
- **Response:** `204 No Content`

## Song Languages

### List Song Languages

- **Endpoint:** `GET /api/admin/song-languages`
- **Description:** Retrieves a list of all song languages.
- **Authentication:** Bearer Token
- **Response:** A JSON array of song language objects:
  ```json
  [
    {
      "id": 1,
      "name": "English",
      "created_at": "2025-07-17T05:50:20.000000Z",
      "updated_at": "2025-07-17T05:50:20.000000Z"
    },
    {
      "id": 2,
      "name": "Spanish",
      "created_at": "2025-07-17T05:50:20.000000Z",
      "updated_at": "2025-07-17T05:50:20.000000Z"
    }
  ]
  ```

### Create Song Language

- **Endpoint:** `POST /api/admin/song-languages`
- **Description:** Creates a new song language.
- **Authentication:** Bearer Token
- **Request Body:**
  ```json
  {
    "name": "New Song Language Name"
  }
  ```
- **Response:** The newly created song language object:
  ```json
  {
    "id": 3,
    "name": "New Song Language Name",
    "created_at": "2025-07-17T05:50:20.000000Z",
    "updated_at": "2025-07-17T05:50:20.000000Z"
  }
  ```

### Show Song Language

- **Endpoint:** `GET /api/admin/song-languages/{song_language}`
- **Description:** Retrieves a single song language by its ID.
- **Authentication:** Bearer Token
- **Response:** The song language object:
  ```json
  {
    "id": 1,
    "name": "Song Language Name",
    "created_at": "2025-07-17T05:50:20.000000Z",
    "updated_at": "2025-07-17T05:50:20.000000Z"
  }
  ```

### Update Song Language

- **Endpoint:** `PUT/PATCH /api/admin/song-languages/{song_language}`
- **Description:** Updates an existing song language.
- **Authentication:** Bearer Token
- **Request Body:**
  ```json
  {
    "name": "Updated Song Language Name"
  }
  ```
- **Response:** The updated song language object:
  ```json
  {
    "id": 1,
    "name": "Updated Song Language Name",
    "created_at": "2025-07-17T05:50:20.000000Z",
    "updated_at": "2025-07-17T05:51:20.000000Z"
  }
  ```

### Delete Song Language

- **Endpoint:** `DELETE /api/admin/song-languages/{song_language}`
- **Description:** Deletes a song language.
- **Authentication:** Bearer Token
- **Response:** `204 No Content`

## Roles

### List Roles

- **Endpoint:** `GET /api/admin/roles`
- **Description:** Retrieves a list of all roles.
- **Authentication:** Bearer Token
- **Response:** A JSON array of role objects:
  ```json
  [
    {
      "id": 1,
      "name": "Super Admin",
      "created_at": "2025-08-14T17:51:19.000000Z",
      "updated_at": "2025-08-14T17:51:19.000000Z"
    },
    {
      "id": 2,
      "name": "Admin",
      "created_at": "2025-08-14T17:51:19.000000Z",
      "updated_at": "2025-08-14T17:51:19.000000Z"
    }
  ]
  ```

## Song Suggestions

### List Song Suggestions

- **Endpoint:** `GET /api/admin/song-suggestions`
- **Description:** Retrieves a paginated list of song suggestions.
- **Authentication:** Bearer Token
- **Query Parameters:**
  - `id` (optional): Filter suggestions by specific ID.
  - `search` (optional): A string to search for in the suggestion title or lyrics.
  - `status` (optional): Filter by status ('pending', 'approved', 'cancelled').
  - `style_id` (optional): Filter suggestions by style ID.
  - `user_id` (optional): Filter suggestions by user ID.
  - `category_ids` (optional): Filter suggestions by category IDs (array or single ID).
  - `song_language_ids` (optional): Filter suggestions by language IDs (array or single ID).
  - `sort_by` (optional): Field to sort by (supported: 'created_at', 'id', 'status').
  - `sort_order` (optional): Sort direction ('asc' or 'desc').
- **Response:** A paginated JSON object with the following structure:
  ```json
  {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Suggested Song Title",
        "youtube": "https://www.youtube.com/watch?v=...",
        "description": "A description of the song.",
        "song_writer": "Songwriter Name",
        "style_id": 1,
        "lyrics": "The lyrics of the song.",
        "music_notes": "The music notes of the song.",
        "status": "pending",
        "user_id": 1,
        "created_at": "2025-12-23T16:50:20.000000Z",
        "updated_at": "2025-12-23T16:50:20.000000Z",
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
        ],
        "song_languages": [
          {
            "id": 1,
            "name": "English",
            "created_at": "2025-07-17T05:50:20.000000Z",
            "updated_at": "2025-07-17T05:50:20.000000Z"
          }
        ],
        "user": {
          "id": 1,
          "name": "User Name",
          "email": "user@example.com",
          "created_at": "2025-07-17T05:50:20.000000Z",
          "updated_at": "2025-07-17T05:50:20.000000Z"
        }
      }
    ],
    "first_page_url": "http://localhost/api/admin/song-suggestions?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost/api/admin/song-suggestions?page=1",
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://localhost/api/admin/song-suggestions?page=1",
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
    "path": "http://localhost/api/admin/song-suggestions",
    "per_page": 15,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
  ```

### Create Song Suggestion

- **Endpoint:** `POST /api/admin/song-suggestions`
- **Description:** Creates a new song suggestion. Note: Email field is not required when creating suggestions.
- **Authentication:** Bearer Token
- **Request Body:**
  ```json
  {
    "title": "New Suggestion Title",
    "youtube": "https://www.youtube.com/watch?v=...",
    "description": "A description of the song.",
    "song_writer": "Songwriter Name",
    "style_id": 1,
    "lyrics": "The lyrics of the song.",
    "music_notes": "The music notes of the song.",
    "user_id": 1,
    "category_ids": [1, 2],
    "song_language_ids": [1, 2]
  }
  ```
- **Response:** The newly created suggestion object:
  ```json
  {
    "id": 1,
    "title": "New Suggestion Title",
    "youtube": "https://www.youtube.com/watch?v=...",
    "description": "A description of the song.",
    "song_writer": "Songwriter Name",
    "style_id": 1,
    "lyrics": "The lyrics of the song.",
    "music_notes": "The music notes of the song.",
    "status": "pending",
    "user_id": 1,
    "created_at": "2025-12-23T16:50:20.000000Z",
    "updated_at": "2025-12-23T16:50:20.000000Z",
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
    ],
    "song_languages": [
      {
        "id": 1,
        "name": "English",
        "created_at": "2025-07-17T05:50:20.000000Z",
        "updated_at": "2025-07-17T05:50:20.000000Z"
      }
    ],
    "user": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com",
      "created_at": "2025-07-17T05:50:20.000000Z",
      "updated_at": "2025-07-17T05:50:20.000000Z"
    }
  }
  ```

### Show Song Suggestion

- **Endpoint:** `GET /api/admin/song-suggestions/{suggestion}`
- **Description:** Retrieves a single song suggestion by its ID.
- **Authentication:** Bearer Token
- **Response:** The suggestion object with related data:
  ```json
  {
    "id": 1,
    "title": "Suggested Song Title",
    "youtube": "https://www.youtube.com/watch?v=...",
    "description": "A description of the song.",
    "song_writer": "Songwriter Name",
    "style_id": 1,
    "lyrics": "The lyrics of the song.",
    "music_notes": "The music notes of the song.",
    "status": "pending",
    "user_id": 1,
    "created_at": "2025-12-23T16:50:20.000000Z",
    "updated_at": "2025-12-23T16:50:20.000000Z",
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
    ],
    "song_languages": [
      {
        "id": 1,
        "name": "English",
        "created_at": "2025-07-17T05:50:20.000000Z",
        "updated_at": "2025-07-17T05:50:20.000000Z"
      }
    ],
    "user": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com",
      "created_at": "2025-07-17T05:50:20.000000Z",
      "updated_at": "2025-07-17T05:50:20.000000Z"
    }
  }
  ```

### Update Song Suggestion

- **Endpoint:** `PUT/PATCH /api/admin/song-suggestions/{suggestion}`
- **Description:** Updates an existing song suggestion.
- **Authentication:** Bearer Token
- **Request Body:** (all fields are optional)
  ```json
  {
    "title": "Updated Suggestion Title",
    "youtube": "https://www.youtube.com/watch?v=...",
    "description": "Updated description",
    "song_writer": "Updated Songwriter",
    "style_id": 1,
    "lyrics": "Updated lyrics",
    "music_notes": "Updated music notes",
    "status": "pending",
    "category_ids": [1, 2, 3],
    "song_language_ids": [1]
  }
  ```
- **Response:** The updated suggestion object:
  ```json
  {
    "id": 1,
    "title": "Updated Suggestion Title",
    "youtube": "https://www.youtube.com/watch?v=...",
    "description": "Updated description",
    "song_writer": "Updated Songwriter",
    "style_id": 1,
    "lyrics": "Updated lyrics",
    "music_notes": "Updated music notes",
    "status": "pending",
    "user_id": 1,
    "created_at": "2025-12-23T16:50:20.000000Z",
    "updated_at": "2025-12-23T16:51:20.000000Z",
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
    ],
    "song_languages": [
      {
        "id": 1,
        "name": "English",
        "created_at": "2025-07-17T05:50:20.000000Z",
        "updated_at": "2025-07-17T05:50:20.000000Z"
      }
    ],
    "user": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com",
      "created_at": "2025-07-17T05:50:20.000000Z",
      "updated_at": "2025-07-17T05:50:20.000000Z"
    }
  }
  ```

### Delete Song Suggestion

- **Endpoint:** `DELETE /api/admin/song-suggestions/{suggestion}`
- **Description:** Deletes a song suggestion (soft delete).
- **Authentication:** Bearer Token
- **Response:** `204 No Content`

### Approve Song Suggestion

- **Endpoint:** `POST /api/admin/song-suggestions/{suggestion}/approve`
- **Description:** Approves a song suggestion and creates a new song from it. After approval, the suggestion status is set to 'approved' and a new song is created in the songs table with all the data from the suggestion.
- **Authentication:** Bearer Token
- **Response:**
  ```json
  {
    "message": "Suggestion approved and song created successfully",
    "suggestion": {
      "id": 1,
      "title": "Approved Song Title",
      "youtube": "https://www.youtube.com/watch?v=...",
      "description": "A description of the song.",
      "song_writer": "Songwriter Name",
      "style_id": 1,
      "lyrics": "The lyrics of the song.",
      "music_notes": "The music notes of the song.",
      "status": "approved",
      "user_id": 1,
      "created_at": "2025-12-23T16:50:20.000000Z",
      "updated_at": "2025-12-23T16:52:20.000000Z",
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
      ],
      "song_languages": [
        {
          "id": 1,
          "name": "English",
          "created_at": "2025-07-17T05:50:20.000000Z",
          "updated_at": "2025-07-17T05:50:20.000000Z"
        }
      ],
      "user": {
        "id": 1,
        "name": "User Name",
        "email": "user@example.com",
        "created_at": "2025-07-17T05:50:20.000000Z",
        "updated_at": "2025-07-17T05:50:20.000000Z"
      }
    },
    "song": {
      "id": 1,
      "code": 1,
      "title": "Approved Song Title",
      "slug": "approved-song-title-1",
      "youtube": "https://www.youtube.com/watch?v=...",
      "description": "A description of the song.",
      "song_writer": "Songwriter Name",
      "style_id": 1,
      "lyrics": "The lyrics of the song.",
      "music_notes": "The music notes of the song.",
      "popular_rating": null,
      "createable_type": "App\\Models\\Admin",
      "createable_id": 1,
      "created_at": "2025-12-23T16:52:20.000000Z",
      "updated_at": "2025-12-23T16:52:20.000000Z",
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
  }
  ```

### Cancel Song Suggestion

- **Endpoint:** `POST /api/admin/song-suggestions/{suggestion}/cancel`
- **Description:** Cancels/rejects a song suggestion. The suggestion status is set to 'cancelled'.
- **Authentication:** Bearer Token
- **Response:**
  ```json
  {
    "message": "Suggestion cancelled successfully",
    "suggestion": {
      "id": 1,
      "title": "Cancelled Song Title",
      "youtube": "https://www.youtube.com/watch?v=...",
      "description": "A description of the song.",
      "song_writer": "Songwriter Name",
      "style_id": 1,
      "lyrics": "The lyrics of the song.",
      "music_notes": "The music notes of the song.",
      "status": "cancelled",
      "user_id": 1,
      "created_at": "2025-12-23T16:50:20.000000Z",
      "updated_at": "2025-12-23T16:53:20.000000Z",
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
      ],
      "song_languages": [
        {
          "id": 1,
          "name": "English",
          "created_at": "2025-07-17T05:50:20.000000Z",
          "updated_at": "2025-07-17T05:50:20.000000Z"
        }
      ],
      "user": {
        "id": 1,
        "name": "User Name",
        "email": "user@example.com",
        "created_at": "2025-07-17T05:50:20.000000Z",
        "updated_at": "2025-07-17T05:50:20.000000Z"
      }
    }
  }
  ```