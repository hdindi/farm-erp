#!/bin/bash

# Complete Farm ERP Setup Script
# Run this on your Digital Ocean droplet after uploading files

set -e

echo "🚀 Starting complete Farm ERP setup..."

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
chmod +x /root/server-setup.sh
/root/server-setup.sh

print_status "Step 2: Configuring production settings..."
# Update configuration
chmod +x /root/production-config.sh
/root/production-config.sh

print_status "Step 3: Setting up Nginx..."
# Configure Nginx
if [ -f "/root/nginx-config" ]; then
    cp /root/nginx-config /etc/nginx/sites-available/farm-erp
    ln -sf /etc/nginx/sites-available/farm-erp /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default
    
    # Test nginx configuration
    if nginx -t; then
        systemctl reload nginx
        print_status "Nginx configured successfully"
    else
        print_error "Nginx configuration test failed"
        exit 1
    fi
else
    print_error "nginx-config file not found in /root/"
    exit 1
fi

print_warning "Step 4: SSL Setup"
echo "Before continuing with SSL setup:"
echo "1. Make sure your domain DNS is pointing to this server"
echo "2. Update ssl-setup.sh with your domain and email"
echo ""
read -p "Have you updated ssl-setup.sh with your domain? (y/n): " ssl_ready

if [[ $ssl_ready =~ ^[Yy]$ ]]; then
    print_status "Setting up SSL certificates..."
    chmod +x /root/ssl-setup.sh
    /root/ssl-setup.sh
else
    print_warning "Skipping SSL setup. You can run it later with: /root/ssl-setup.sh"
fi

print_warning "Step 5: Application Setup"
echo "Before setting up the application, make sure you have:"
echo "1. Your repository URL (GitHub/GitLab)"
echo "2. Database connection details from Digital Ocean"
echo "3. Updated .env.production.updated file"
echo ""
read -p "Do you want to proceed with application setup? (y/n): " app_ready

if [[ $app_ready =~ ^[Yy]$ ]]; then
    print_status "Setting up Farm ERP application..."
    
    # Prompt for repository URL
    read -p "Enter your Git repository URL: " repo_url
    
    if [ ! -z "$repo_url" ]; then
        # Clone repository
        if [ -d "/var/www/farm-erp" ]; then
            print_warning "Application directory exists. Backing up..."
            mv /var/www/farm-erp /var/www/farm-erp.backup.$(date +%s)
        fi
        
        git clone $repo_url /var/www/farm-erp
        cd /var/www/farm-erp
        
        # Copy environment file
        if [ -f "/root/.env.production.updated" ]; then
            cp /root/.env.production.updated .env
            print_status "Environment file copied"
        else
            print_warning "No updated environment file found. Please create .env manually"
        fi
        
        # Set permissions
        chown -R www-data:www-data /var/www/farm-erp
        chmod -R 755 /var/www/farm-erp/storage
        chmod -R 755 /var/www/farm-erp/bootstrap/cache
        
        print_status "Running application setup..."
        chmod +x /root/initial-setup.sh
        /root/initial-setup.sh
    else
        print_error "Repository URL is required"
    fi
else
    print_warning "Skipping application setup. You can run it later with: /root/initial-setup.sh"
fi

print_status "Step 6: Setting up monitoring and backups..."
if [ -f "/root/monitoring-setup.sh" ]; then
    chmod +x /root/monitoring-setup.sh
    /root/monitoring-setup.sh
fi

if [ -f "/root/backup.sh" ]; then
    chmod +x /root/backup.sh
    # Update backup script with actual database credentials
    print_warning "Don't forget to update /root/backup.sh with your database credentials"
fi

print_status "🎉 Setup completed!"
echo ""
echo "Next steps:"
echo "1. Update .env file with your actual database credentials: nano /var/www/farm-erp/.env"
echo "2. Update backup script with database details: nano /root/backup.sh"
echo "3. Test your application at: https://your-domain.com"
echo "4. Set up GitHub Actions with your server details"
echo ""
echo "Important files:"
echo "- Application: /var/www/farm-erp/"
echo "- Nginx config: /etc/nginx/sites-available/farm-erp"
echo "- Environment: /var/www/farm-erp/.env"
echo "- Logs: /var/log/nginx/ and /var/www/farm-erp/storage/logs/"
echo ""
print_status "Your Farm ERP server setup is complete! 🌾"