# Postman Collection for Calvary Songs Book API

This directory contains the Postman collection for testing the Calvary Songs Book API.

## Import Instructions

1. Open Postman
2. Click on **Import** button in the top left
3. Select the `Calvary_Songs_Book_API.postman_collection.json` file
4. The collection will be imported with all endpoints

## Environment Variables

The collection uses the following variables:

- **base_url**: The base URL of the API (default: `http://localhost:8000`)
- **token**: The authentication token obtained from the login endpoint

### Setting Up Environment

1. After importing, create a new environment in Postman
2. Add the `base_url` variable with your API URL
3. Add the `token` variable (this will be automatically populated after login if you use the test script)

Alternatively, you can update the collection variables:
1. Right-click on the collection name
2. Click **Edit**
3. Go to **Variables** tab
4. Update `base_url` to match your environment

## Using the Collection

### Authentication

1. **Login**: Use the Login endpoint to authenticate
   - Update the email and password in the request body
   - After successful login, copy the token from the response
   - Set it in the `token` environment/collection variable

2. **Protected Endpoints**: Endpoints that require authentication will automatically use the `{{token}}` variable

### Testing the Suggest Songs API

The collection includes two examples for the suggest songs endpoint:

1. **Submit Song Suggestion**: Full example with all fields
2. **Submit Song Suggestion (Required Only)**: Minimal example with only required fields

Required fields:
- `code` (integer)
- `title` (string)
- `lyrics` (string)
- `email` (string, valid email format)

Optional fields:
- `youtube`, `description`, `song_writer`, `style_id`, `key`, `music_notes`, `popular_rating`

### Endpoints Overview

#### Public Endpoints (No Authentication Required)
- GET `/api/songs` - List songs with optional filters
- GET `/api/songs/{slug}` - Get single song by slug
- GET `/api/categories` - List all categories
- GET `/api/search-filters` - Get search filter options
- POST `/api/suggest-songs` - Submit a song suggestion
- POST `/api/check-version` - Check for app updates
- POST `/api/login` - Authenticate user

#### Protected Endpoints (Authentication Required)
- POST `/api/logout` - Logout user
- GET `/api/user` - Get authenticated user details

## Response Codes

- `200` - Success
- `201` - Created (for POST requests)
- `204` - No Content (for DELETE requests)
- `401` - Unauthorized (authentication required)
- `404` - Not Found
- `422` - Validation Error

## Support

For more detailed API documentation, refer to:
- `USER_API_DOCUMENTATION.MD` - User API documentation
- `ADMIN_API_DOCUMENTATION.MD` - Admin API documentation
