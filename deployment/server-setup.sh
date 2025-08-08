#!/bin/bash

# Server Setup Script for Farm ERP - Digital Ocean Ubuntu 22.04
# Run this script on your fresh Ubuntu droplet

set -e

echo "Starting server setup for Farm ERP..."

# Update system packages
apt update && apt upgrade -y

# Install required packages
apt install -y software-properties-common curl wget git unzip

# Add PHP repository
add-apt-repository ppa:ondrej/php -y
apt update

# Install PHP 8.2 and extensions
apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-gd php8.2-mbstring php8.2-zip php8.2-intl php8.2-bcmath php8.2-soap

# Install Nginx
apt install -y nginx

# Install MySQL client (for database operations)
apt install -y mysql-client

# Install Node.js and npm
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create application directory
mkdir -p /var/www/farm-erp
chown -R www-data:www-data /var/www/farm-erp

# Configure PHP-FPM
sed -i 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/' /etc/php/8.2/fpm/php.ini

# Start and enable services
systemctl start php8.2-fpm
systemctl enable php8.2-fpm
systemctl start nginx
systemctl enable nginx

# Setup firewall
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

echo "Server setup completed successfully!"
echo "Next: Configure your domain and nginx virtual host"