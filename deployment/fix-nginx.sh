#!/bin/bash

# Fix Nginx Configuration
echo "🔧 Fixing Nginx configuration..."

# Remove the problematic config
rm -f /etc/nginx/sites-enabled/farm-erp

# Use the simplified config
cp /root/deployment/nginx-config-simple /etc/nginx/sites-available/farm-erp

# Enable the site
ln -sf /etc/nginx/sites-available/farm-erp /etc/nginx/sites-enabled/

# Remove default site
rm -f /etc/nginx/sites-enabled/default

# Test the configuration
if nginx -t; then
    echo "✅ Nginx configuration is valid"
    systemctl reload nginx
    echo "✅ Nginx reloaded successfully"
    echo "🌐 Your application should be accessible at: http://128.199.60.244"
else
    echo "❌ Nginx configuration test failed"
    echo "Checking what went wrong..."
    nginx -t
fi