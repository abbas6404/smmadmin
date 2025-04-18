# SMM Admin Panel

A comprehensive Social Media Marketing (SMM) administration panel built with Laravel, featuring account management for Facebook and Gmail, Chrome profile management, and order processing capabilities.

## Features

- **Account Management**
  - Facebook account management
  - Gmail account management
  - Chrome profile management
  - Batch submission handling

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

## Requirements

- PHP >= 8.1
- Laravel 10.x
- MySQL/MariaDB
- Composer
- Node.js & NPM

## Installation

1. Clone the repository:
```bash
git clone https://github.com/abbas6404/smm_controller.git
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

## Usage

1. Access the admin panel at: `http://localhost:8000/admin`
2. Default admin credentials:
   - Email: admin@example.com
   - Password: password

## Security

If you discover any security-related issues, please email [your-email] instead of using the issue tracker.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
