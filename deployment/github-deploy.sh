#!/bin/bash

# GitHub Actions Deployment Script for Farm ERP
# This script runs on the server during automated deployments

set -e

echo "🚀 Starting automated deployment..."

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${GREEN}[DEPLOY]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Navigate to application directory
cd /var/www/farm-erp

print_status "Pulling latest changes from repository..."
git pull origin main

print_status "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

print_status "Installing NPM dependencies and building assets..."
npm ci --production=false
npm run build

print_status "Setting proper permissions..."
chown -R www-data:www-data /var/www/farm-erp
chmod -R 755 storage bootstrap/cache

print_status "Clearing and caching configuration..."
php artisan config:clear
php artisan config:cache
php artisan view:cache
# Skip route:cache due to duplicate route names

print_status "Running database migrations..."
php artisan migrate --force

print_status "Clearing application cache..."
php artisan cache:clear

print_status "Restarting services..."
systemctl reload php8.2-fpm
systemctl reload nginx

print_status "✅ Deployment completed successfully!"
echo "🌐 Application is live at: http://128.199.60.244"