# Task Management System API

A robust RESTful API for managing tasks with role-based access control, built with Laravel and Laravel Sanctum.

## Features

- **Authentication**: Secure API authentication using Laravel Sanctum
- **Role-based Access Control**: Different permissions for Managers and Users
- **Task Management**: Create, read, update, and delete tasks
- **Task Dependencies**: Support for task dependencies with circular dependency prevention
- **Filtering**: Filter tasks by status, assigned user, and due date range
- **Validation**: Comprehensive data validation and error handling
- **Database Migrations**: Automated database schema management
- **Seeders**: Pre-populated test data

## Requirements

- PHP 8.2 or higher
- Composer
- SQLite (default) or MySQL/PostgreSQL
- Laravel 12.x

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd task-management-system
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate --seed
   ```

5. **Start the development server**
   ```bash
   php artisan serve
   ```

The API will be available at `http://localhost:8000`

## API Endpoints

### Authentication

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/login` | Login user | No |
| POST | `/api/logout` | Logout user | Yes |
| GET | `/api/me` | Get current user | Yes |

### Tasks

| Method | Endpoint | Description | Auth Required | Role Required |
|--------|----------|-------------|---------------|---------------|
| GET | `/api/tasks` | Get all tasks | Yes | - |
| POST | `/api/tasks` | Create task | Yes | Manager |
| GET | `/api/tasks/{id}` | Get task by ID | Yes | - |
| PUT | `/api/tasks/{id}` | Update task | Yes | Manager/User* |
| DELETE | `/api/tasks/{id}` | Delete task | Yes | Manager |

*Users can only update task status for tasks assigned to them

### Task Dependencies

| Method | Endpoint | Description | Auth Required | Role Required |
|--------|----------|-------------|---------------|---------------|
| POST | `/api/task-dependencies` | Add dependency | Yes | Manager |
| GET | `/api/tasks/{id}/dependencies` | Get task dependencies | Yes | - |
| DELETE | `/api/task-dependencies/{id}` | Remove dependency | Yes | Manager |

## Authentication

The API uses Laravel Sanctum for stateless authentication. Include the token in the Authorization header:

```
Authorization: Bearer {your-token}
```

### Login Example

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "manager1@example.com",
    "password": "password"
  }'
```

## Role-based Access Control

### Manager Role
- Can create, update, and delete tasks
- Can assign tasks to any user
- Can manage task dependencies
- Can view all tasks

### User Role
- Can only view tasks assigned to them
- Can only update the status of tasks assigned to them
- Cannot create, delete, or manage dependencies

## Task Dependencies

Tasks can have dependencies on other tasks. Key rules:

1. A task cannot depend on itself
2. Circular dependencies are prevented
3. A task cannot be completed until all its dependencies are completed
4. Only managers can manage dependencies

## Filtering Tasks

The `/api/tasks` endpoint supports filtering:

- `status`: Filter by task status (pending, in_progress, completed, cancelled)
- `assigned_to`: Filter by assigned user ID
- `due_date_from` & `due_date_to`: Filter by due date range

Example:
```
GET /api/tasks?status=pending&assigned_to=3&due_date_from=2024-01-01&due_date_to=2024-12-31
```

## Sample Data

The system comes with pre-seeded data:

### Users
- **Managers**: manager1@example.com, manager2@example.com
- **Users**: user1@example.com, user2@example.com, user3@example.com
- **Password**: `password` (for all users)

### Tasks
- 6 sample tasks with various statuses and assignments
- Task dependencies demonstrating the dependency system

## Testing

### Using Postman

1. Import the provided `Task_Management_API.postman_collection.json`
2. Set the `base_url` variable to `http://localhost:8000`
3. Login to get a token and set it in the `token` variable
4. Test all endpoints

### Using cURL

```bash
# Login
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"manager1@example.com","password":"password"}' \
  | jq -r '.token')

# Get all tasks
curl -H "Authorization: Bearer $TOKEN" http://localhost:8000/api/tasks

# Create a task
curl -X POST http://localhost:8000/api/tasks \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "New Task",
    "description": "Task description",
    "due_date": "2024-12-31 23:59:59",
    "assigned_to": 3
  }'
```

## Database Schema

The system uses the following main tables:

- **users**: User accounts with roles
- **tasks**: Task information
- **task_dependencies**: Task dependency relationships
- **personal_access_tokens**: Sanctum authentication tokens

See `ERD.md` for the complete Entity Relationship Diagram.

## Error Handling

The API returns consistent error responses:

```json
{
  "message": "Error description",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

Common HTTP status codes:
- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `422`: Validation Error
- `500`: Server Error

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

### Database Reset
```bash
php artisan migrate:fresh --seed
```

## Security Features

- **CSRF Protection**: Enabled for state-changing operations
- **Rate Limiting**: Built-in Laravel rate limiting
- **Input Validation**: Comprehensive validation on all inputs
- **SQL Injection Prevention**: Using Eloquent ORM
- **XSS Protection**: Input sanitization
- **Authentication**: Secure token-based authentication

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support, please contact the development team or create an issue in the repository."# Task-Management-System-API" 
