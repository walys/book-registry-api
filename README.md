# Book Registry API

A RESTful API built with Laravel for managing books, featuring token-based authentication, CRUD operations, input validation, and user-specific data access.

## Features

- User authentication using Laravel Sanctum.
- Login and logout endpoints.
- Full CRUD operations for books.
- Input validation using Laravel Form Requests.
- User-specific data access and ownership protection.
- JSON responses for API requests.
- Automated feature tests.

## Tech Stack

- PHP 8.3
- Laravel 13
- Laravel Sanctum
- MySQL for development
- SQLite in-memory database for automated tests
- PHPUnit

## Requirements

- PHP compatible with the project's dependencies.
- Composer.
- MySQL.
- Required PHP extensions for Laravel and the project.

## Installation

### 1. Clone the repository

```bash
git clone YOUR_REPOSITORY_URL
cd book-registry-api
```

Replace `YOUR_REPOSITORY_URL` with the actual repository URL.

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment variables

Create your environment file:

```bash
cp .env.example .env
```

On Windows, you can also copy `.env.example` to `.env` manually.

Generate the application key:

```bash
php artisan key:generate
```

Configure your database connection in the `.env` file:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=book_registry_api
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Create the configured database in MySQL, then run the migrations:

```bash
php artisan migrate
```

```bash
php artisan db:seed
```

### 4. Start the development server

```bash
php artisan serve
```

By default, the API will be available at:

`http://127.0.0.1:8000`

> **Security note:** Never commit your `.env` file, passwords, access tokens, or other sensitive information to version control.

## Authentication

The API uses Laravel Sanctum personal access tokens for authentication.

Protected endpoints require a Bearer token in the request headers:

```http
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

### Login

**Endpoint:** `POST /api/login`

Request body:

```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

A successful response includes an access token, token type, user information, and a confirmation message.

Use the returned token to authenticate subsequent requests.

### Logout

**Endpoint:** `POST /api/logout`

Requires authentication. Revokes the current access token.

### Get the authenticated user

**Endpoint:** `GET /api/user`

Returns information about the currently authenticated user.

## Book Endpoints

All book endpoints require authentication.

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/books` | List the authenticated user's books |
| POST | `/api/books` | Create a book |
| GET | `/api/books/{id}` | Retrieve a specific book |
| PUT | `/api/books/{id}` | Update a book |
| PATCH | `/api/books/{id}` | Partially update a book |
| DELETE | `/api/books/{id}` | Delete a book |

### Create a Book

**Endpoint:** `POST /api/books`

Request body:

```json
{
  "name": "Clean Code",
  "isbn": "9780132350884",
  "value": 99.90
}
```

Example successful response:

```json
{
  "name": "Clean Code",
  "isbn": "9780132350884",
  "value": "99.90"
}
```

The actual response also includes the model's other serialized fields, such as its ID, user ID, and timestamps.

### Validation Rules

| Field | Rules |
|---|---|
| `name` | Required when creating a book; must be a string with a maximum length of 255 characters |
| `isbn` | Optional; must contain digits only when provided |
| `value` | Optional; must be numeric and greater than or equal to zero |

Additional behavior:

- ISBN values are stored as strings to preserve leading zeros.
- During updates, omitted fields remain unchanged.
- If `name` is provided during an update, it cannot be empty.
- Optional `isbn` and `value` fields can be set to `null`.

## Data Ownership and Security

Each book belongs to a user through the `User`–`Book` relationship.

Book queries are scoped to the authenticated user, preventing users from accessing or modifying books owned by other accounts.

Requests for books that do not exist or do not belong to the authenticated user return `404 Not Found`.

Unauthenticated requests to protected endpoints return `401 Unauthorized`.

## HTTP Status Codes

| Status Code | Description |
|---|---|
| `200 OK` | Request completed successfully |
| `201 Created` | Resource created successfully |
| `401 Unauthorized` | Authentication is missing or invalid |
| `404 Not Found` | Resource not found or not accessible to the current user |
| `422 Unprocessable Entity` | Validation failed |

## Running Tests

Run the automated test suite:

```bash
php artisan test
```

The feature tests cover:

- Authentication and authorization.
- Successful login and logout.
- Book creation, retrieval, listing, updating, and deletion.
- Input validation.
- Partial updates.
- User-specific data access.
- JSON error responses.
- Protection against unauthorized access.

Tests use an in-memory SQLite database, as configured in the test environment, to isolate test data from the development database.

## Architecture

The application separates responsibilities across several layers:

- **Controllers:** Handle HTTP requests and build API responses.
- **Form Requests:** Define and enforce request validation rules.
- **Services:** Encapsulate book-related application operations.
- **Models:** Represent database entities and their relationships.
- **Feature Tests:** Verify API behavior through HTTP requests.

This structure improves maintainability, readability, and testability.

## License

Refer to the repository's `LICENSE` file, if available, for licensing information.