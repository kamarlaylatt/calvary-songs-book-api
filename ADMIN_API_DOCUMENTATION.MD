# Admin API Documentation

This documentation provides details about the available API endpoints for administrators.

## Authentication

### Login

-   **Endpoint:** `POST /api/admin/login`
-   **Description:** Authenticates an admin user and returns an API token.
-   **Request Body:**
    ```json
    {
        "email": "admin@example.com",
        "password": "password"
    }
    ```
-   **Response:**
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

-   **Endpoint:** `POST /api/admin/logout`
-   **Description:** Logs out the authenticated admin user.
-   **Authentication:** Bearer Token
-   **Response:**
    ```json
    {
        "message": "Logout successful"
    }
    ```

### Admin Detail

-   **Endpoint:** `GET /api/admin`
-   **Description:** Retrieves the details of the authenticated admin user.
-   **Authentication:** Bearer Token
-   **Response:**
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

-   **Endpoint:** `GET /api/admin/admins`
-   **Description:** Retrieves a paginated list of admins.
-   **Authentication:** Bearer Token
-   **Response:** A paginated JSON object with the following structure:
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

-   **Endpoint:** `POST /api/admin/admins`
-   **Description:** Creates a new admin.
-   **Authentication:** Bearer Token
-   **Request Body:**
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
-   **Response:** The newly created admin object:
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

-   **Endpoint:** `GET /api/admin/admins/{admin}`
-   **Description:** Retrieves a single admin by their ID.
-   **Authentication:** Bearer Token
-   **Response:** The admin object with related roles:
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

-   **Endpoint:** `PUT /api/admin/admins/{admin}`
-   **Description:** Updates the details of an existing admin.
-   **Authentication:** Bearer Token
-   **Request Body:**
    ```json
    {
        "name": "Updated Admin Name",
        "email": "admin@example.com",
        "status": "inactive",
        "roles": [2]
    }
    ```
-   **Response:** The updated admin object:
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

-   **Endpoint:** `DELETE /api/admin/admins/{admin}`
-   **Description:** Deletes an existing admin.
-   **Authentication:** Bearer Token
-   **Response:**
    ```json
    {
        "message": "Admin deleted successfully"
    }
    ```

## Songs

### List Songs

-   **Endpoint:** `GET /api/admin/songs`
-   **Description:** Retrieves a paginated list of songs.
-   **Authentication:** Bearer Token
-   **Query Parameters:**
    -   `id` (optional): Filter songs by specific ID.
    -   `search` (optional): A string to search for in the song title or lyrics.
    -   `style_id` (optional): Filter songs by style ID.
    -   `category_ids` (optional): Filter songs by category IDs (array or single ID).
    -   `song_language_ids` (optional): Filter songs by language IDs (array or single ID).
    -   `sort_by` (optional): Field to sort by (supported: 'created_at', 'id').
    -   `sort_order` (optional): Sort direction ('asc' or 'desc').
-   **Response:** A paginated JSON object with the following structure:
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

-   **Endpoint:** `POST /api/admin/songs`
-   **Description:** Creates a new song.
-   **Authentication:** Bearer Token
-   **Request Body:**
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
-   **Response:** The newly created song object with auto-generated fields:
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

-   **Endpoint:** `GET /api/admin/songs/{song}`
-   **Description:** Retrieves a single song by its ID.
-   **Authentication:** Bearer Token
-   **Response:** The song object with related style:
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

-   **Endpoint:** `PUT/PATCH /api/admin/songs/{song}`
-   **Description:** Updates an existing song.
-   **Authentication:** Bearer Token
-   **Request Body:** (same as create, but all fields are optional; `style_id` is nullable and, if present, must exist in styles table)
-   **Response:** The updated song object:
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

-   **Endpoint:** `DELETE /api/admin/songs/{song}`
-   **Description:** Deletes a song.
-   **Authentication:** Bearer Token
-   **Response:** `204 No Content`

## Styles

### List Styles

-   **Endpoint:** `GET /api/admin/styles`
-   **Description:** Retrieves a list of all styles.
-   **Authentication:** Bearer Token
-   **Response:** A JSON array of style objects:
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

-   **Endpoint:** `POST /api/admin/styles`
-   **Description:** Creates a new style.
-   **Authentication:** Bearer Token
-   **Request Body:**
    ```json
    {
        "name": "New Style Name"
    }
    ```
-   **Response:** The newly created style object:
    ```json
    {
        "id": 3,
        "name": "New Style Name",
        "created_at": "2025-07-17T05:50:20.000000Z",
        "updated_at": "2025-07-17T05:50:20.000000Z"
    }
    ```

### Show Style

-   **Endpoint:** `GET /api/admin/styles/{style}`
-   **Description:** Retrieves a single style by its ID.
-   **Authentication:** Bearer Token
-   **Response:** The style object:
    ```json
    {
        "id": 1,
        "name": "Style Name",
        "created_at": "2025-07-17T05:50:20.000000Z",
        "updated_at": "2025-07-17T05:50:20.000000Z"
    }
    ```

### Update Style

-   **Endpoint:** `PUT/PATCH /api/admin/styles/{style}`
-   **Description:** Updates an existing style.
-   **Authentication:** Bearer Token
-   **Request Body:**
    ```json
    {
        "name": "Updated Style Name"
    }
    ```
-   **Response:** The updated style object:
    ```json
    {
        "id": 1,
        "name": "Updated Style Name",
        "created_at": "2025-07-17T05:50:20.000000Z",
        "updated_at": "2025-07-17T05:51:20.000000Z"
    }
    ```

### Delete Style

-   **Endpoint:** `DELETE /api/admin/styles/{style}`
-   **Description:** Deletes a style.
-   **Authentication:** Bearer Token
-   **Response:** `204 No Content`

## Categories

### List Categories

-   **Endpoint:** `GET /api/admin/categories`
-   **Description:** Retrieves a paginated list of categories sorted by `sort_no` (ascending) and then by creation date (latest first).
-   **Authentication:** Bearer Token
-   **Response:** A paginated JSON object with category data:
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

-   **Endpoint:** `POST /api/admin/categories`
-   **Description:** Creates a new category. The `sort_no` field controls the display order of categories (lower numbers appear first).
-   **Authentication:** Bearer Token
-   **Request Body:**
    ```json
    {
        "name": "New Category Name",
        "description": "Optional category description",
        "sort_no": 1
    }
    ```
-   **Response:** The newly created category object:
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

-   **Endpoint:** `GET /api/admin/categories/{category}`
-   **Description:** Retrieves a single category by its ID.
-   **Authentication:** Bearer Token
-   **Response:** The category object:
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

-   **Endpoint:** `PUT/PATCH /api/admin/categories/{category}`
-   **Description:** Updates an existing category. The `sort_no` field can be used to change the display order of categories.
-   **Authentication:** Bearer Token
-   **Request Body:** (same as create, but fields are optional)
    ```json
    {
        "name": "Updated Category Name",
        "description": "Updated description",
        "sort_no": 2
    }
    ```
-   **Response:** The updated category object:
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

-   **Endpoint:** `DELETE /api/admin/categories/{category}`
-   **Description:** Deletes a category.
-   **Authentication:** Bearer Token
-   **Response:** `204 No Content`

## Song Languages

### List Song Languages

-   **Endpoint:** `GET /api/admin/song-languages`
-   **Description:** Retrieves a list of all song languages.
-   **Authentication:** Bearer Token
-   **Response:** A JSON array of song language objects:
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

-   **Endpoint:** `POST /api/admin/song-languages`
-   **Description:** Creates a new song language.
-   **Authentication:** Bearer Token
-   **Request Body:**
    ```json
    {
        "name": "New Song Language Name"
    }
    ```
-   **Response:** The newly created song language object:
    ```json
    {
        "id": 3,
        "name": "New Song Language Name",
        "created_at": "2025-07-17T05:50:20.000000Z",
        "updated_at": "2025-07-17T05:50:20.000000Z"
    }
    ```

### Show Song Language

-   **Endpoint:** `GET /api/admin/song-languages/{song_language}`
-   **Description:** Retrieves a single song language by its ID.
-   **Authentication:** Bearer Token
-   **Response:** The song language object:
    ```json
    {
        "id": 1,
        "name": "Song Language Name",
        "created_at": "2025-07-17T05:50:20.000000Z",
        "updated_at": "2025-07-17T05:50:20.000000Z"
    }
    ```

### Update Song Language

-   **Endpoint:** `PUT/PATCH /api/admin/song-languages/{song_language}`
-   **Description:** Updates an existing song language.
-   **Authentication:** Bearer Token
-   **Request Body:**
    ```json
    {
        "name": "Updated Song Language Name"
    }
    ```
-   **Response:** The updated song language object:
    ```json
    {
        "id": 1,
        "name": "Updated Song Language Name",
        "created_at": "2025-07-17T05:50:20.000000Z",
        "updated_at": "2025-07-17T05:51:20.000000Z"
    }
    ```

### Delete Song Language

-   **Endpoint:** `DELETE /api/admin/song-languages/{song_language}`
-   **Description:** Deletes a song language.
-   **Authentication:** Bearer Token
-   **Response:** `204 No Content`

## Roles

### List Roles

-   **Endpoint:** `GET /api/admin/roles`
-   **Description:** Retrieves a list of all roles.
-   **Authentication:** Bearer Token
-   **Response:** A JSON array of role objects:
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

## Suggest Songs

### List Suggestions

-   **Endpoint:** `GET /api/admin/suggest-songs`
-   **Description:** Retrieves a paginated list of suggested songs.
-   **Authentication:** Bearer Token
-   **Query Parameters (optional):**
    -   `status`: Filter by suggestion status (`0` cancelled, `1` pending, `2` approved)
    -   `search`: Search by title or lyrics
    -   `style_id`: Filter by style id
    -   `category_id`: Filter by category id
    -   `song_language_id`: Filter by song language id
-   **Response:** Paginated list of suggestion objects.

### Update Suggestion

-   **Endpoint:** `PUT /api/admin/suggest-songs/{suggestSong}`
-   **Description:** Updates a suggested song record.
-   **Authentication:** Bearer Token
-   **Request Body (any of the following):**
    -   `title` (string)
    -   `youtube` (string)
    -   `description` (string)
    -   `song_writer` (string)
    -   `style_id` (integer)
    -   `key` (string)
    -   `lyrics` (string)
    -   `music_notes` (string)
    -   `popular_rating` (integer 0-5)
    -   `email` (string, optional)
    -   `category_ids` (array, optional): Array of category IDs to associate with the suggested song
    -   `song_language_ids` (array, optional): Array of song language IDs to associate with the suggested song
-   **Response:** Updated suggestion object including `categories` and `songLanguages`.

### Approve Suggestion

-   **Endpoint:** `POST /api/admin/suggest-songs/{suggestSong}/approve`
-   **Description:** Approves a suggestion, creates a song record from its data, and sets status to `2`.
-   **Authentication:** Bearer Token
-   **Response:**
    ```json
    {
        "message": "Suggestion approved and song created",
        "suggestion": { "id": 1, "status": 2 },
        "song": {
            "id": 10,
            "title": "Sample",
            "code": 100,
            "slug": "sample-100",
            "categories": [{ "id": 1, "name": "Worship" }],
            "song_languages": []
        }
    }
    ```

### Cancel Suggestion

-   **Endpoint:** `POST /api/admin/suggest-songs/{suggestSong}/cancel`
-   **Description:** Cancels a suggestion by setting status to `0`.
-   **Authentication:** Bearer Token
-   **Response:**
    ```json
    {
        "message": "Suggestion cancelled",
        "suggestion": { "id": 1, "status": 0 }
    }
    ```
