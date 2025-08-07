# Deploying to Render.com

## Prerequisites

1. **GitHub Repository**: Your code should be pushed to GitHub
2. **Render Account**: Sign up at [render.com](https://render.com)

## Deployment Steps

### 1. Create a New Web Service on Render

1. Go to your Render dashboard
2. Click "New +" and select "Web Service"
3. Connect your GitHub repository
4. Select the `lana-amawi-coaching-portal` repository

### 2. Configure the Web Service

**Important**: When Render auto-detects Node.js, you need to manually change the settings:

1. **Environment**: Change from "Node" to **"PHP"**
2. **Build Command**: `composer install --no-dev --optimize-autoloader`
3. **Start Command**: `vendor/bin/heroku-php-apache2 public/`
4. **Root Directory**: Leave empty (or `/` if needed)

### 3. Environment Variables

Set these environment variables in Render:

```
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-generated-key
APP_NAME="Lana Amawi Coaching Portal"
APP_URL=https://your-app-name.onrender.com
LOG_CHANNEL=stack
LOG_LEVEL=debug
DB_CONNECTION=postgres
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
QUEUE_CONNECTION=sync
```

### 4. Database Configuration

1. Create a new PostgreSQL database on Render
2. Add the database URL to your environment variables:
   ```
   DATABASE_URL=postgres://username:password@host:port/database
   ```

### 5. Email Configuration

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

### 6. Deploy

1. Click "Create Web Service"
2. Render will automatically build and deploy your application
3. Wait for the build to complete
4. Your app will be available at the provided URL

## Post-Deployment

1. **Run Migrations**: The deployment script will handle this
2. **Seed Database**: If needed, run `php artisan db:seed`
3. **Test the Application**: Verify all features work correctly

## Troubleshooting

- **Build Failures**: Check the build logs for dependency issues
- **Database Connection**: Ensure DATABASE_URL is correctly set
- **500 Errors**: Check the application logs in Render dashboard
- **File Permissions**: The deployment script handles this automatically

## Custom Domain (Optional)

1. Go to your web service settings
2. Click "Custom Domains"
3. Add your domain and configure DNS 