#!/bin/bash

# Generate application key if not set
php artisan key:generate --force

# Clear and cache config
php artisan config:clear
php artisan config:cache

# Clear and cache routes
php artisan route:clear
php artisan route:cache

# Clear and cache views
php artisan view:clear
php artisan view:cache

# Run migrations
php artisan migrate --force

# Start Laravel development server
php artisan serve --host=0.0.0.0 --port=$PORT 