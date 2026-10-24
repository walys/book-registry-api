
# Book Registry API

A RESTful API built with Laravel for managing books, featuring token-based authentication, CRUD operations, request validation, and user-specific data access.

## Documentation

**[View Full Documentation](docs/index.html)**

Explore the installation guide, authentication flow, API endpoints, validation rules, security, and automated tests.

## Features

- Token-based authentication with Laravel Sanctum.
- Complete book CRUD operations.
- Request validation using Form Requests.
- User-specific data access and ownership protection.
- Consistent JSON API responses.
- Automated feature tests.

## Tech Stack

| Technology | Purpose |
|---|---|
| PHP 8.3 | Backend language |
| Laravel 13 | Application framework |
| Laravel Sanctum | API authentication |
| MySQL | Development database |
| SQLite | In-memory testing database |
| PHPUnit | Automated testing |

## Requirements

- PHP 8.3 or a compatible version.
- Composer.
- MySQL.
- Required PHP extensions.

## Getting Started

Clone the repository and install the dependencies:

```bash
git clone YOUR_REPOSITORY_URL
cd book-registry-api
composer install
```

Configure the environment:

```bash
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env`, create the database, and run:

```bash
php artisan migrate
php artisan db:seed
php artisan serve
```

The API will be available at:

`http://127.0.0.1:8000`

> **Note:** Replace `YOUR_REPOSITORY_URL` with your actual repository URL. On Windows, you can copy `.env.example` manually and rename the copy to `.env`.

## API Endpoints

Authentication:

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/login` | Authenticate and receive an access token |
| POST | `/api/logout` | Revoke the current access token |
| GET | `/api/user` | Retrieve the authenticated user |

Books — authentication required:

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/books` | List the user's books |
| POST | `/api/books` | Create a book |
| GET | `/api/books/{id}` | Retrieve a book |
| PUT | `/api/books/{id}` | Update a book |
| PATCH | `/api/books/{id}` | Partially update a book |
| DELETE | `/api/books/{id}` | Delete a book |

## Running Tests

Run the automated test suite:

```bash
php artisan test
```

The tests use an in-memory SQLite database.

## Security

- Protected endpoints require a valid Bearer token.
- Users can only access and manage their own books.
- Validation errors are returned in JSON format.
- Environment files, passwords, and access tokens must not be committed to version control.

## Project Structure

- **Controllers:** Handle HTTP requests and responses.
- **Form Requests:** Validate incoming data.
- **Services:** Encapsulate book management operations.
- **Models:** Define data structures and relationships.
- **Feature Tests:** Verify API behavior and access restrictions.
- **Documentation:** `docs/index.html`

## License

Refer to the `LICENSE` file if one is provided.
