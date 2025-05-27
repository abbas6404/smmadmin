# SMM Admin Panel

A comprehensive Social Media Marketing (SMM) administration panel built with Laravel, featuring account management for Facebook and Gmail, Chrome profile management, order processing capabilities, and a Facebook Quick Check API for Chrome extensions.

## Features

- **Account Management**
  - Facebook account management
  - Gmail account management
  - Chrome profile management
  - Batch submission handling
  - Facebook Quick Check for account validation
  - Facebook account usage limits and tracking

- **User Management**
  - User registration and authentication
  - Role-based access control
  - User activity tracking

- **Order Processing**
  - Order creation and management
  - Service management
  - Payment processing
  - Order status tracking

- **Dashboard**
  - Real-time statistics
  - Revenue tracking
  - Account status monitoring
  - Activity logs

- **API Integrations**
  - PC profile authentication API
  - Facebook account management API
  - Order processing API
  - Facebook Quick Check API for Chrome extensions
  - Auto-shutdown functionality for PC clients

## Requirements

- PHP >= 8.1
- Laravel 10.x
- MySQL/MariaDB
- Composer
- Node.js & NPM

## Installation

1. Clone the repository:
```bash
git clone https://github.com/abbas6404/smmadmin.git
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run database migrations and seeders:
```bash
php artisan migrate --seed
```

7. Start the development server:
```bash
php artisan serve
```

## Facebook Account Usage Limits

The system includes a feature to limit how many times a Facebook account can be used per day:

- Each Facebook account has a `use_count` field that tracks daily usage
- A global `facebook_account_daily_use_limit` setting controls the maximum allowed uses (default: 4)
- Accounts that reach their daily limit are automatically excluded from order processing
- Use counts are reset daily at a configurable time

### Setting Up the Facebook Account Reset Scheduler

#### On Windows Development Environment:

1. Create a batch file `reset_facebook.bat`:
```
@echo off
cd C:\path\to\smmadmin
php artisan facebook:reset-use-counts
```

2. Schedule it using Windows Task Scheduler:
   - Create a new Basic Task
   - Set to run daily at your desired time (e.g., 8:10 AM)
   - Action: Start a program
   - Program: path to your batch file

#### On Linux/Unix Production Server:

Add a cron job:
```
# Reset Facebook account use counts daily at 8:10 AM
10 8 * * * cd /path/to/smmadmin && php artisan facebook:reset-use-counts >> /dev/null 2>&1
```

#### Manual Reset:

You can manually reset all Facebook account use counts with:
```bash
php artisan facebook:reset-use-counts
```

## API Documentation

The application includes several APIs for integration with external systems:

- **PC Profile API**: Authentication and management of PC profiles
- **Facebook Account API**: CRUD operations for Facebook accounts
- **Order Management API**: Order processing and status updates
- **Facebook Quick Check API**: API for Chrome extensions to validate Facebook accounts

API documentation is available in the admin panel at `/admin/api/docs`.

## Facebook Quick Check API

This API allows Chrome extensions to get and update Facebook accounts for validation:

### Endpoints

- **GET /api/facebook-quick-check/get-account**: Retrieve a pending account for checking
- **POST /api/facebook-quick-check/update-check/{id}**: Update an account's status after checking

### Authentication

All Facebook Quick Check API requests require an API key in the header:
```
X-API-KEY: YOUR_API_KEY
```

Configure your API key in `config/services.php` or via the `.env` file.

## Auto-Shutdown Feature

The system includes an auto-shutdown feature for PC clients:

- Each PC profile has an `auto_shutdown` boolean flag
- When enabled, the API will return a `shutdown` status in responses
- PC clients should check for this status and initiate shutdown when received
- This helps manage resources by shutting down PCs when they're not needed

## Usage

1. Access the admin panel at: `http://localhost:8000/admin`
2. Default admin credentials:
   - Email: admin@example.com
   - Password: password

## Security

If you discover any security-related issues, please email [your-email] instead of using the issue tracker.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
