#!/bin/bash

# UAT Environment Setup Script for Farm ERP
# Run this script on your server to set up the UAT environment

set -e

echo "🚀 Setting up UAT environment for Farm ERP..."

# Configuration
UAT_DIR="/var/www/farm-erp-uat"
NGINX_SITES_AVAILABLE="/etc/nginx/sites-available"
NGINX_SITES_ENABLED="/etc/nginx/sites-enabled"
REPO_URL="git@github.com:hdindi/farm-erp.git"  # SSH URL for authentication
DB_NAME="farm_erp_uat"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    print_error "Please run this script as root (use sudo)"
    exit 1
fi

print_status "Starting UAT environment setup..."

# 1. Create UAT directory and clone repository
print_status "Setting up UAT directory..."
if [ -d "$UAT_DIR" ]; then
    print_warning "UAT directory already exists. Backing it up..."
    mv "$UAT_DIR" "${UAT_DIR}_backup_$(date +%Y%m%d_%H%M%S)"
fi

mkdir -p "$UAT_DIR"
cd "$UAT_DIR"

print_status "Cloning repository..."
git clone "$REPO_URL" .
git checkout uat

# 2. Install dependencies
print_status "Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

# 3. Create Laravel directories
print_status "Creating Laravel directories..."
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# 4. Set up environment file
print_status "Setting up environment configuration..."
if [ ! -f ".env.uat" ]; then
    print_error ".env.uat file not found in repository!"
    exit 1
fi

cp .env.uat .env

# 5. Generate application key
print_status "Generating application key..."
php artisan key:generate --force

# 6. Set permissions
print_status "Setting proper permissions..."
chown -R www-data:www-data "$UAT_DIR"
chmod -R 755 "$UAT_DIR"
chmod -R 775 "$UAT_DIR/storage"
chmod -R 775 "$UAT_DIR/bootstrap/cache"

# 7. Create storage link
print_status "Creating storage link..."
sudo -u www-data php artisan storage:link

# 8. Set up database
print_status "Setting up UAT database..."
read -p "Enter MySQL root password: " -s mysql_password
echo

mysql -u root -p"$mysql_password" <<EOF
CREATE DATABASE IF NOT EXISTS $DB_NAME;
GRANT ALL PRIVILEGES ON $DB_NAME.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
EOF

print_status "Database created successfully!"

# Update .env with database password
read -p "Enter database password for UAT environment: " -s db_password
echo
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$db_password/" .env

# 9. Run migrations
print_status "Running database migrations..."
sudo -u www-data php artisan migrate --force

# 10. Seed database (optional)
read -p "Do you want to seed the database with sample data? (y/n): " -r
if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_status "Seeding database..."
    sudo -u www-data php artisan db:seed --force
fi

# 11. Create Nginx configuration
print_status "Creating Nginx configuration..."
cat > "$NGINX_SITES_AVAILABLE/uat-kinjabi-farm" <<EOF
server {
    listen 80;
    server_name uat.kinjabi.farm;
    root $UAT_DIR/public;

    index index.php index.html;

    # UAT environment header
    add_header X-Environment "UAT" always;
    add_header X-Robots-Tag "noindex, nofollow" always;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Deny access to sensitive files
    location ~ /\.ht {
        deny all;
    }

    location ~ /\.env {
        deny all;
    }

    # Block access to version control
    location ~ /\.git {
        deny all;
    }
}
EOF

# 12. Enable Nginx site
if [ ! -L "$NGINX_SITES_ENABLED/uat-kinjabi-farm" ]; then
    ln -s "$NGINX_SITES_AVAILABLE/uat-kinjabi-farm" "$NGINX_SITES_ENABLED/"
    print_status "Nginx site enabled"
fi

# 13. Test Nginx configuration
print_status "Testing Nginx configuration..."
nginx -t

if [ $? -eq 0 ]; then
    print_status "Nginx configuration is valid"
    systemctl reload nginx
    print_status "Nginx reloaded"
else
    print_error "Nginx configuration test failed!"
    exit 1
fi

# 14. Set up SSL (Let's Encrypt)
print_status "Setting up SSL certificate..."
read -p "Enter your email for SSL certificate: " ssl_email

if command -v certbot &> /dev/null; then
    certbot --nginx -d uat.kinjabi.farm --email "$ssl_email" --agree-tos --no-eff-email --non-interactive
    print_status "SSL certificate installed"
else
    print_warning "Certbot not found. Please install SSL manually:"
    echo "sudo apt install certbot python3-certbot-nginx"
    echo "sudo certbot --nginx -d uat.kinjabi.farm --email $ssl_email --agree-tos --no-eff-email"
fi

# 15. Optimize Laravel
print_status "Optimizing Laravel application..."
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# 16. Final service restart
print_status "Restarting services..."
systemctl restart php8.2-fpm
systemctl restart nginx

print_status "🎉 UAT environment setup completed!"
echo
echo "==========================================";
echo "UAT Environment Details:"
echo "==========================================";
echo "URL: https://uat.kinjabi.farm"
echo "Directory: $UAT_DIR"
echo "Database: $DB_NAME"
echo "Branch: uat"
echo "Environment: UAT"
echo "==========================================";
echo
print_warning "Next steps:"
echo "1. Add DNS record for uat.kinjabi.farm at Namecheap"
echo "2. Set up GitHub secrets for automated deployment"
echo "3. Test the UAT environment"
echo
print_status "Setup completed successfully!"
