#!/bin/bash

# Complete Farm ERP Setup Script - Fixed Version
# Run this on your Digital Ocean droplet after uploading files

set -e

echo "🚀 Starting Farm ERP setup (Fixed Version)..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    print_error "Please run as root (use sudo)"
    exit 1
fi

print_status "Step 1: Installing server dependencies..."
# Make server setup executable and run
if [ -f "/root/deployment/server-setup.sh" ]; then
    chmod +x /root/deployment/server-setup.sh
    /root/deployment/server-setup.sh
else
    print_error "server-setup.sh not found!"
    exit 1
fi

print_status "Step 2: Cleaning up and configuring Nginx..."
# Use the clean nginx setup
chmod +x /root/deployment/clean-nginx.sh
/root/deployment/clean-nginx.sh

print_warning "Step 3: Skipping SSL Setup (IP Address Testing)"
echo "SSL certificates cannot be issued for IP addresses."
echo "Your application will be available at: http://128.199.60.244"
echo ""

print_status "Step 4: Application Setup"

# Check if application directory exists
if [ -d "/var/www/farm-erp" ]; then
    print_warning "Application directory exists. Do you want to remove it and start fresh? (y/n)"
    read -r remove_existing
    if [[ $remove_existing =~ ^[Yy]$ ]]; then
        rm -rf /var/www/farm-erp
        print_status "Removed existing application directory"
    fi
fi

# Prompt for repository URL
read -p "Enter your Git repository URL: " repo_url

if [ ! -z "$repo_url" ]; then
    print_status "Cloning repository..."
    git clone $repo_url /var/www/farm-erp
    cd /var/www/farm-erp
    
    # Copy environment file
    if [ -f "/root/deployment/.env.production.harris" ]; then
        cp /root/deployment/.env.production.harris .env
        print_status "Environment file copied (configured for IP address)"
    else
        print_warning "No environment file found. Creating basic .env"
        cp .env.example .env
        # Set basic database connection
        sed -i "s/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/" .env
        sed -i "s/DB_HOST=127.0.0.1/DB_HOST=dbaas-db-2779280-do-user-24512826-0.m.db.ondigitalocean.com/" .env
        sed -i "s/DB_PORT=3306/DB_PORT=25060/" .env
        sed -i "s/DB_DATABASE=laravel/DB_DATABASE=farm-erp-db/" .env
        sed -i "s/DB_USERNAME=root/DB_USERNAME=doadmin/" .env
        sed -i "s/DB_PASSWORD=/DB_PASSWORD=YOUR_DATABASE_PASSWORD_HERE/" .env
    fi
    
    # Install Composer dependencies
    print_status "Installing Composer dependencies..."
    if command -v composer &> /dev/null; then
        composer install --no-dev --optimize-autoloader --no-interaction
    else
        print_error "Composer not found! Installing composer..."
        curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
        composer install --no-dev --optimize-autoloader --no-interaction
    fi
    
    # Generate application key
    php artisan key:generate
    
    # Install NPM dependencies and build
    print_status "Installing NPM dependencies and building assets..."
    if command -v npm &> /dev/null; then
        npm ci --production=false
        npm run build
    else
        print_warning "NPM not found, skipping asset build"
    fi
    
    # Set permissions
    print_status "Setting file permissions..."
    chown -R www-data:www-data /var/www/farm-erp
    chmod -R 755 /var/www/farm-erp/storage
    chmod -R 755 /var/www/farm-erp/bootstrap/cache
    
    # Create storage symlink
    php artisan storage:link
    
    # Test database connection before migrations
    print_status "Testing database connection..."
    if php artisan migrate:status > /dev/null 2>&1; then
        print_status "✅ Database connection successful!"
        
        print_status "Running database migrations..."
        php artisan migrate --force
        
        print_status "Do you want to run database seeders? (y/n)"
        read -r run_seeders
        if [[ $run_seeders =~ ^[Yy]$ ]]; then
            php artisan db:seed --force
        fi
    else
        print_warning "⚠️ Database connection failed. Please check your .env configuration."
        print_warning "You can run migrations later with: cd /var/www/farm-erp && php artisan migrate"
    fi
    
    # Cache configuration
    print_status "Caching configuration..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Restart PHP-FPM
    systemctl restart php8.2-fpm
    
else
    print_warning "Skipping application setup. Repository URL was not provided."
fi

print_status "🎉 Setup completed!"
echo ""
echo "✅ Your Farm ERP application should be ready!"
echo "🌐 Access your application at: http://128.199.60.244"
echo ""
echo "Final checks:"
echo "- Nginx status: $(systemctl is-active nginx)"
echo "- PHP-FPM status: $(systemctl is-active php8.2-fpm)"
echo ""
echo "If you encounter issues:"
echo "- Check logs: tail -f /var/log/nginx/farm-erp.error.log"
echo "- Check Laravel logs: tail -f /var/www/farm-erp/storage/logs/laravel.log"
echo "- Test database: cd /var/www/farm-erp && php artisan migrate:status"
echo ""
print_status "Your Farm ERP server setup is complete! 🌾"