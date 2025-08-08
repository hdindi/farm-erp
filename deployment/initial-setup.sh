#!/bin/bash

# Initial Application Setup Script
# Run this once after cloning the repository to the server

set -e

APP_PATH="/var/www/farm-erp"
REPO_URL="https://github.com/yourusername/farm-erp.git"

echo "Setting up Farm ERP application..."

# Create application directory
mkdir -p $APP_PATH

# Clone repository
cd /var/www
git clone $REPO_URL farm-erp

cd $APP_PATH

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Install NPM dependencies
npm ci --production=false

# Copy environment file
cp .env.production .env

echo "Please edit the .env file with your actual configuration:"
echo "- Database credentials"
echo "- Domain name"
echo "- Mail configuration"
echo ""
echo "nano .env"
echo ""
read -p "Press Enter after editing .env file..."

# Generate application key
php artisan key:generate

# Set proper permissions
chown -R www-data:www-data $APP_PATH
chmod -R 755 $APP_PATH/storage
chmod -R 755 $APP_PATH/bootstrap/cache

# Create storage symlink
php artisan storage:link

# Run migrations and seeders
echo "Running database migrations..."
php artisan migrate

echo "Do you want to run database seeders? (y/n)"
read -r run_seeders
if [[ $run_seeders =~ ^[Yy]$ ]]; then
    php artisan db:seed
fi

# Build assets
npm run build

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "Initial setup completed!"
echo "Your Farm ERP application should now be accessible at your domain."