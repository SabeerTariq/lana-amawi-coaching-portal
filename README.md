# Lana Amawi Coaching Portal

A Laravel-based coaching management system with admin and client portals.

## Features

- **Admin Portal**: Dashboard, client management, appointment scheduling, messaging
- **Client Portal**: Personal dashboard, appointment viewing, messaging with admin
- **Authentication**: User registration, login, password reset
- **Appointment Management**: Booking, scheduling, status tracking
- **Messaging System**: Real-time communication between admin and clients
- **Email Notifications**: Automated email sending for credentials and notifications

## Local Development Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL/PostgreSQL
- Node.js (for frontend assets)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/SabeerTariq/lana-amawi-coaching-portal.git
   cd lana-amawi-coaching-portal
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   - Update `.env` file with your database credentials
   - Run migrations: `php artisan migrate`
   - Seed the database: `php artisan db:seed`

6. **Build frontend assets**
   ```bash
   npm run dev
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

### Default Admin Account

After running the seeders, you can login with:
- **Email**: admin@lanaamawi.com
- **Password**: password

### Development Commands

```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear cache
php artisan config:clear
php artisan cache:clear

# Run tests
php artisan test

# Build assets for production
npm run build
```

## Project Structure

```
lana-amawi/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   ├── Models/              # Eloquent models
│   └── Mail/                # Email classes
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   └── views/              # Blade templates
├── routes/
│   └── web.php             # Web routes
└── public/                 # Public assets
```

## Technology Stack

- **Backend**: Laravel 12, PHP 8.2
- **Frontend**: Blade templates, Bootstrap, JavaScript
- **Database**: MySQL/PostgreSQL
- **Email**: Laravel Mail with SMTP
- **Authentication**: Laravel's built-in auth system

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This project is licensed under the MIT License.
