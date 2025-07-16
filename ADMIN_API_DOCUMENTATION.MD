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
    "token": "your-api-token"
  }
  ```

### Logout

- **Endpoint:** `POST /api/admin/logout`
- **Description:** Logs out the authenticated admin user.
- **Authentication:** Bearer Token

### Admin Detail

- **Endpoint:** `GET /api/admin`
- **Description:** Retrieves the details of the authenticated admin user.
- **Authentication:** Bearer Token

## Songs

### List Songs

- **Endpoint:** `GET /api/admin/songs`
- **Description:** Retrieves a paginated list of songs.
- **Authentication:** Bearer Token
- **Query Parameters:**
  - `search` (optional): A string to search for in the song title, songwriter, or lyrics.
- **Response:** A paginated JSON object of songs.

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
    "style_id": 1,
    "lyrics": "The lyrics of the song.",
    "music_notes": "The music notes of the song."
  }
  ```
- **Response:** The newly created song object.

### Show Song

- **Endpoint:** `GET /api/admin/songs/{song}`
- **Description:** Retrieves a single song by its ID.
- **Authentication:** Bearer Token
- **Response:** The song object.

### Update Song

- **Endpoint:** `PUT/PATCH /api/admin/songs/{song}`
- **Description:** Updates an existing song.
- **Authentication:** Bearer Token
- **Request Body:** (same as create, but fields are optional)
- **Response:** The updated song object.

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
- **Response:** A JSON array of style objects.

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
- **Response:** The newly created style object.

### Show Style

- **Endpoint:** `GET /api/admin/styles/{style}`
- **Description:** Retrieves a single style by its ID.
- **Authentication:** Bearer Token
- **Response:** The style object.

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
- **Response:** The updated style object.

### Delete Style

- **Endpoint:** `DELETE /api/admin/styles/{style}`
- **Description:** Deletes a style.
- **Authentication:** Bearer Token
- **Response:** `204 No Content`
