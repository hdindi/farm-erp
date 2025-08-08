#!/bin/bash

# Production Configuration Script for Farm ERP
# Replace these values with your actual details

echo "Farm ERP Production Configuration"
echo "================================="

# Get your actual values and update this script
DROPLET_IP="YOUR_DROPLET_IP_HERE"
DOMAIN="YOUR_DOMAIN_HERE"  # e.g., farm-erp.yourdomain.com
DB_HOST="YOUR_DB_HOST_HERE"  # from Digital Ocean database overview
DB_PORT="25060"  # Usually 25060 for DO managed databases
DB_NAME="farm-erp"
DB_USER="doadmin"
DB_PASSWORD="YOUR_DB_PASSWORD_HERE"  # from database overview
EMAIL="your-email@domain.com"  # for SSL certificates

echo "Configuration Values:"
echo "Droplet IP: $DROPLET_IP"
echo "Domain: $DOMAIN"
echo "Database Host: $DB_HOST"
echo "Database Port: $DB_PORT"
echo "Admin Email: $EMAIL"
echo ""
echo "Next Steps:"
echo "1. Update the values above in this script"
echo "2. Update .env.production with these values"
echo "3. Update nginx-config with your domain"
echo "4. Update ssl-setup.sh with your domain and email"
echo "5. Upload all files to your server"

# Create updated .env file
cat > .env.production.updated << EOF
APP_NAME="Farm ERP"
APP_ENV=production
APP_KEY=base64:REPLACE_WITH_GENERATED_KEY
APP_DEBUG=false
APP_TIMEZONE=Africa/Nairobi
APP_URL=https://$DOMAIN

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error
LOG_STACK=single

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=$DB_HOST
DB_PORT=$DB_PORT
DB_DATABASE=$DB_NAME
DB_USERNAME=$DB_USER
DB_PASSWORD=$DB_PASSWORD
DB_SSLMODE=require

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.$DOMAIN

# Cache Configuration
CACHE_STORE=database

# Queue Configuration
QUEUE_CONNECTION=database

# Mail Configuration (Update with your email provider)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=$EMAIL
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=$EMAIL
MAIL_FROM_NAME="\${APP_NAME}"

# Security Settings
BCRYPT_ROUNDS=12

# File Storage
FILESYSTEM_DISK=local
EOF

echo ""
echo "Updated .env file created: .env.production.updated"
echo "Review and rename it to .env when deploying"