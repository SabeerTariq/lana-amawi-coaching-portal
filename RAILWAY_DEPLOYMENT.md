# Deploying to Railway.com

## Prerequisites

1. **GitHub Repository**: Your code should be pushed to GitHub
2. **Railway Account**: Sign up at [railway.app](https://railway.app)

## Deployment Steps

### 1. Create a New Project on Railway

1. Go to your Railway dashboard
2. Click "New Project"
3. Select "Deploy from GitHub repo"
4. Connect your GitHub repository
5. Select the `lana-amawi-coaching-portal` repository

### 2. Configure Environment Variables

Add these environment variables in Railway:

```
APP_ENV=production
APP_DEBUG=false
APP_NAME="Lana Amawi Coaching Portal"
APP_URL=https://your-app-name.railway.app
LOG_CHANNEL=stack
LOG_LEVEL=debug
DB_CONNECTION=postgres
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
QUEUE_CONNECTION=sync
```

### 3. Database Configuration

1. In Railway dashboard, click "New" → "Database" → "PostgreSQL"
2. Railway will automatically provide the database URL
3. Add the database URL to your environment variables:
   ```
   DATABASE_URL=postgresql://username:password@host:port/database
   ```

### 4. Email Configuration

Update these variables with your email settings:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Lana Amawi Coaching Portal"
```

### 5. Deploy

1. Railway will automatically detect the configuration files
2. The build process will:
   - Install PHP 8.2 and required extensions
   - Run `composer install --no-dev --optimize-autoloader`
   - Generate application key
   - Cache configurations
   - Start the Laravel development server

### 6. Post-Deployment

1. **Run Migrations**: 
   ```bash
   php artisan migrate --force
   ```

2. **Seed Database** (if needed):
   ```bash
   php artisan db:seed
   ```

3. **Test the Application**: Verify all features work correctly

## Troubleshooting

### Build Failures
- **Composer Issues**: Check if all dependencies are compatible
- **PHP Version**: Ensure PHP 8.2 is being used
- **Memory Issues**: Railway provides sufficient memory for Laravel

### Runtime Issues
- **Database Connection**: Ensure DATABASE_URL is correctly set
- **500 Errors**: Check the application logs in Railway dashboard
- **Port Issues**: Railway automatically sets the PORT environment variable

### Common Solutions
1. **Clear Cache**: `php artisan config:clear && php artisan cache:clear`
2. **Regenerate Key**: `php artisan key:generate --force`
3. **Check Logs**: View logs in Railway dashboard

## Configuration Files

- **`railway.json`**: Railway-specific configuration
- **`nixpacks.toml`**: Build configuration for Railway
- **`Procfile`**: Process definition for Railway
- **`.php-version`**: PHP version specification

## Custom Domain (Optional)

1. Go to your project settings in Railway
2. Click "Custom Domains"
3. Add your domain and configure DNS 