# Quick Start Guide

## Prerequisites

Make sure you have the following installed:
- PHP 8.2+
- Composer
- Node.js and npm
- MySQL Server

## Quick Setup

### Windows Users
1. Double-click `setup.bat` or run it from command prompt
2. Follow the prompts to complete setup

### Linux/Mac Users
1. Run `./setup.sh` in terminal
2. Follow the prompts to complete setup

### Manual Setup
If the setup scripts don't work, follow these steps:

1. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure database**
   - Create a MySQL database named `lana_amawi`
   - Update `.env` file with your database credentials

4. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Build assets**
   ```bash
   npm run build
   ```

## Starting the Application

### Development Mode (with hot reloading)
```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Start Vite development server
npm run dev
```

### Production Mode
```bash
# Build assets for production
npm run build

# Start Laravel server
php artisan serve
```

## Accessing the Application

- **URL**: http://localhost:8000
- **Admin Login**: admin@example.com / password
- **Demo Login**: demo@example.com / password

## Features

- ✅ Responsive Bootstrap design
- ✅ User authentication system
- ✅ Contact form with validation
- ✅ Modern UI with Font Awesome icons
- ✅ Mobile-friendly layout
- ✅ Database migrations and seeders
- ✅ Asset compilation with Vite

## Next Steps

1. Customize the design in `resources/css/app.css`
2. Add new pages by creating controllers and views
3. Extend the database with new migrations
4. Add more features to the application

## Troubleshooting

### Common Issues

1. **Database connection error**
   - Make sure MySQL is running
   - Check database credentials in `.env`
   - Ensure database `lana_amawi` exists

2. **Asset compilation error**
   - Run `npm install` to install dependencies
   - Run `npm run build` to compile assets

3. **Permission errors**
   - Make sure storage and bootstrap/cache directories are writable
   - Run `php artisan cache:clear` to clear cache

### Getting Help

- Check the main README.md for detailed documentation
- Review Laravel documentation at https://laravel.com/docs
- Check Bootstrap documentation at https://getbootstrap.com/docs 