#!/bin/bash

# Farm ERP Production Deployment Script
# This script handles the application deployment on the server

set -e

APP_PATH="/var/www/farm-erp"
PHP_VERSION="8.2"

echo "Starting Farm ERP deployment..."

# Navigate to application directory
cd $APP_PATH

# Enable maintenance mode
php artisan down --retry=60 --secret=farm-erp-maintenance

echo "Application is in maintenance mode"

# Pull latest code from repository
git pull origin main

echo "Code updated from repository"

# Install/Update Composer dependencies (production optimized)
composer install --no-dev --optimize-autoloader --no-interaction

echo "Composer dependencies installed"

# Install/Update NPM dependencies and build assets
npm ci --production=false
npm run build

echo "Assets compiled"

# Set proper permissions
chown -R www-data:www-data $APP_PATH
chmod -R 755 $APP_PATH/storage
chmod -R 755 $APP_PATH/bootstrap/cache

echo "Permissions set"

# Clear and cache configuration
php artisan config:clear
php artisan config:cache

# Clear and cache routes
php artisan route:clear
php artisan route:cache

# Clear and cache views
php artisan view:clear
php artisan view:cache

# Run database migrations
php artisan migrate --force

echo "Database migrations completed"

# Clear application cache
php artisan cache:clear

# Restart PHP-FPM to ensure all changes are loaded
systemctl reload php$PHP_VERSION-fpm

# Restart queue workers (if using queues)
# php artisan queue:restart

echo "Services restarted"

# Disable maintenance mode
php artisan up

echo "Farm ERP deployment completed successfully!"
echo "Application is now live at: https://farm-erp.yourdomain.com"