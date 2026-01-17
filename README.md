# Track App API

A RESTful API built with Symfony 8 for managing music tracks. This application provides CRUD operations for tracks with validation, following best practices with service layer separation.

## 🚀 Features

- **CRUD Operations**: Create, read, update, and delete tracks
- **Validation**: Built-in validation for track properties
- **Service Layer**: Business logic separated into service classes
- **RESTful API**: Standard HTTP methods and status codes
- **ISRC Support**: Optional ISRC (International Standard Recording Code) validation
- **JSON Responses**: All endpoints return JSON format

## 📋 Requirements

- PHP >= 8.4
- Composer
- PostgreSQL or MySQL database
- Symfony 8.0

## 📦 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd track-app
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment variables**
   
   Copy `.env` file and configure your database:
   ```bash
   cp .env .env.local
   ```
   
   Update `.env.local` with your database credentials:
   ```env
   DATABASE_URL="postgresql://user:password@127.0.0.1:5432/track_app?serverVersion=16&charset=utf8"
   # or for MySQL:
   # DATABASE_URL="mysql://user:password@127.0.0.1:3306/track_app?serverVersion=8.0"
   ```

4. **Create the database**
   ```bash
   php bin/console doctrine:database:create
   ```

5. **Run migrations**
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

6. **Load fixtures (optional)**
   ```bash
   php bin/console doctrine:fixtures:load
   ```

## 🗄️ Database

The application uses Doctrine ORM with migrations. The main entity is `Track` with the following properties:

- `id`: Auto-incremented integer (primary key)
- `title`: String (required, max 255 characters)
- `artist`: String (required, max 255 characters)
- `duration`: Integer (required, in seconds)
- `isrc`: String (optional, max 255 characters, must match ISRC format)

### Migrations

Create a new migration:
```bash
php bin/console make:migration
```

Apply migrations:
```bash
php bin/console doctrine:migrations:migrate
```

## 🛣️ API Endpoints

Base URL: `http://localhost:8000` (or your server URL)

### Get All Tracks
```http
GET /api/tracks
```

**Response (200 OK):**
```json
[
  {
    "id": 1,
    "title": "Example Track",
    "artist": "Example Artist",
    "duration": 240,
    "isrc": "US-RC1-12-34567"
  }
]
```

### Get Single Track
```http
GET /api/tracks/{id}
```

**Response (200 OK):**
```json
{
  "id": 1,
  "title": "Example Track",
  "artist": "Example Artist",
  "duration": 240,
  "isrc": "US-RC1-12-34567"
}
```

**Response (404 Not Found):**
```json
{
  "message": "Track not found"
}
```

### Create Track
```http
POST /api/tracks
Content-Type: application/json
```

**Request Body:**
```json
{
  "title": "New Track",
  "artist": "New Artist",
  "duration": 180,
  "isrc": "US-RC1-12-34567"
}
```

**Response (201 Created):**
```json
{
  "id": 2,
  "title": "New Track",
  "artist": "New Artist",
  "duration": 180,
  "isrc": "US-RC1-12-34567"
}
```

**Response (422 Unprocessable Entity) - Validation Error:**
```json
{
  "message": "Validation failed",
  "errors": {
    "title": "Title is required",
    "duration": "Duration must be an integer"
  }
}
```

### Update Track
```http
PUT /api/tracks/{id}
Content-Type: application/json
```

**Request Body:**
```json
{
  "title": "Updated Track",
  "artist": "Updated Artist",
  "duration": 200,
  "isrc": "US-RC1-12-34567"
}
```

**Response (200 OK):**
```json
{
  "id": 1,
  "title": "Updated Track",
  "artist": "Updated Artist",
  "duration": 200,
  "isrc": "US-RC1-12-34567"
}
```

**Response (404 Not Found):**
```json
{
  "message": "Track not found"
}
```

**Response (422 Unprocessable Entity) - Validation Error:**
```json
{
  "message": "Validation failed",
  "errors": {
    "isrc": "ISRC must match the format: XX-XXX-XX-XXXXX (e.g., US-RC1-12-34567)"
  }
}
```

### Delete Track
```http
DELETE /api/tracks/{id}
```

**Response (204 No Content):** Empty body

**Response (404 Not Found):**
```json
{
  "message": "Track not found"
}
```

## ✅ Validation Rules

### Track Entity Validation

- **title**: Required, not blank
- **artist**: Required, not blank
- **duration**: Required, must be an integer
- **isrc**: Optional, but if provided must match the pattern: `^[A-Z]{2}-[A-Z0-9]{3}-\d{2}-\d{5}$`
  - Example valid ISRC: `US-RC1-12-34567`

## 📁 Project Structure

```
track-app/
├── config/              # Symfony configuration files
│   ├── packages/        # Package-specific configurations
│   └── routes/          # Route definitions
├── migrations/          # Database migration files
├── public/              # Web server document root
│   └── index.php       # Application entry point
├── src/
│   ├── Controller/     # HTTP controllers
│   │   └── TrackController.php
│   ├── Entity/         # Doctrine entities
│   │   └── Track.php
│   ├── Repository/     # Doctrine repositories
│   │   └── TrackRepository.php
│   ├── Service/        # Business logic services
│   │   └── TrackService.php
│   └── DataFixtures/   # Test data fixtures
│       └── AppFixtures.php
└── var/                # Cache and logs (gitignored)
```

## 🧪 Testing the API

### Using cURL

**Get all tracks:**
```bash
curl http://localhost:8000/api/tracks
```

**Create a track:**
```bash
curl -X POST http://localhost:8000/api/tracks \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test Track",
    "artist": "Test Artist",
    "duration": 180,
    "isrc": "US-RC1-12-34567"
  }'
```

**Update a track:**
```bash
curl -X PUT http://localhost:8000/api/tracks/1 \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Updated Title",
    "artist": "Updated Artist",
    "duration": 200
  }'
```

**Delete a track:**
```bash
curl -X DELETE http://localhost:8000/api/tracks/1
```

## 🛠️ Development

### Run the development server
```bash
symfony server:start
# or
php -S localhost:8000 -t public
```

### Clear cache
```bash
php bin/console cache:clear
```

### Check routes
```bash
php bin/console debug:router
```

## 🏗️ Architecture

This application follows a layered architecture:

1. **Controller Layer** (`TrackController`): Handles HTTP requests/responses
2. **Service Layer** (`TrackService`): Contains business logic and validation
3. **Repository Layer** (`TrackRepository`): Database access abstraction
4. **Entity Layer** (`Track`): Domain model with validation constraints

This separation ensures:
- Testability: Each layer can be tested independently
- Maintainability: Changes to business logic don't affect controllers
- Reusability: Services can be used by different controllers or commands

## 📚 Technologies Used

- **Symfony 8.0**: PHP framework
- **Doctrine ORM 3.6**: Object-relational mapping
- **PostgreSQL/MySQL**: Database
- **Symfony Serializer**: JSON serialization with groups
- **Symfony Validator**: Entity validation

## 📝 License

Proprietary

## 🤝 Contributing

1. Create a feature branch
2. Make your changes
3. Ensure all tests pass
4. Submit a pull request

---

For more information about Symfony, visit [symfony.com](https://symfony.com)
