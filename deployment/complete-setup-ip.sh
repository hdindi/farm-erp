#!/bin/bash

# Complete Farm ERP Setup Script for IP Address Testing
# Run this on your Digital Ocean droplet after uploading files

set -e

echo "🚀 Starting Farm ERP setup for IP address testing..."

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
chmod +x /root/deployment/server-setup.sh
/root/deployment/server-setup.sh

print_status "Step 2: Configuring Nginx for IP address..."
# Configure Nginx for IP address
if [ -f "/root/deployment/nginx-config" ]; then
    cp /root/deployment/nginx-config /etc/nginx/sites-available/farm-erp
    ln -sf /etc/nginx/sites-available/farm-erp /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default
    
    # Test nginx configuration
    if nginx -t; then
        systemctl reload nginx
        print_status "Nginx configured successfully for IP address"
    else
        print_error "Nginx configuration test failed"
        exit 1
    fi
else
    print_error "nginx-config file not found in /root/deployment/"
    exit 1
fi

print_warning "Step 3: Skipping SSL Setup (IP Address Testing)"
echo "SSL certificates cannot be issued for IP addresses."
echo "Your application will be available at: http://128.199.60.244"
echo ""

print_status "Step 4: Application Setup"
echo "Setting up Farm ERP application..."

# Prompt for repository URL
read -p "Enter your Git repository URL (or press Enter to skip): " repo_url

if [ ! -z "$repo_url" ]; then
    # Clone repository
    if [ -d "/var/www/farm-erp" ]; then
        print_warning "Application directory exists. Backing up..."
        mv /var/www/farm-erp /var/www/farm-erp.backup.$(date +%s)
    fi
    
    git clone $repo_url /var/www/farm-erp
    cd /var/www/farm-erp
    
    # Copy environment file
    if [ -f "/root/deployment/.env.production.harris" ]; then
        cp /root/deployment/.env.production.harris .env
        print_status "Environment file copied (configured for IP address)"
    else
        print_warning "No environment file found. Creating basic .env"
        cp .env.example .env
    fi
    
    # Install Composer dependencies
    print_status "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
    
    # Generate application key
    php artisan key:generate
    
    # Install NPM dependencies and build
    print_status "Installing NPM dependencies and building assets..."
    npm ci --production=false
    npm run build
    
    # Set permissions
    chown -R www-data:www-data /var/www/farm-erp
    chmod -R 755 /var/www/farm-erp/storage
    chmod -R 755 /var/www/farm-erp/bootstrap/cache
    
    # Create storage symlink
    php artisan storage:link
    
    print_status "Running database migrations..."
    echo "Make sure your database is accessible before continuing."
    read -p "Press Enter to run database migrations (or Ctrl+C to cancel): "
    
    # Run migrations
    php artisan migrate
    
    print_status "Do you want to run database seeders? (y/n)"
    read -r run_seeders
    if [[ $run_seeders =~ ^[Yy]$ ]]; then
        php artisan db:seed
    fi
    
    # Cache configuration
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
else
    print_warning "Skipping application setup. Repository URL was not provided."
fi

print_status "Step 5: Setting up monitoring..."
if [ -f "/root/deployment/monitoring-setup.sh" ]; then
    chmod +x /root/deployment/monitoring-setup.sh
    /root/deployment/monitoring-setup.sh
fi

print_status "🎉 Setup completed!"
echo ""
echo "✅ Your Farm ERP application is ready!"
echo "🌐 Access your application at: http://128.199.60.244"
echo ""
echo "Important notes:"
echo "- Application runs on HTTP (no SSL for IP addresses)"
echo "- For production, set up a domain name and SSL"
echo "- Database credentials are configured in /var/www/farm-erp/.env"
echo ""
echo "Next steps:"
echo "1. Test your application: http://128.199.60.244"
echo "2. Set up your domain name (optional)"
echo "3. Configure GitHub Actions for automated deployments"
echo ""
print_status "Your Farm ERP server setup is complete! 🌾"