#!/bin/bash

# Clean up and fix Nginx configuration completely
echo "🧹 Completely cleaning up Nginx configuration..."

# Stop nginx first
systemctl stop nginx

# Remove all farm-erp related configs
rm -f /etc/nginx/sites-enabled/farm-erp
rm -f /etc/nginx/sites-available/farm-erp

# Check for any remaining references
echo "Checking for any remaining farm-erp configs..."
find /etc/nginx -name "*farm*" -type f | xargs rm -f

# Create a completely fresh, simple configuration
cat > /etc/nginx/sites-available/farm-erp << 'EOF'
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    
    server_name 128.199.60.244 _;
    root /var/www/farm-erp/public;
    
    index index.php index.html index.htm;
    
    # Basic Laravel setup
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP processing
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Deny access to .htaccess files
    location ~ /\.ht {
        deny all;
    }
    
    # Simple logging
    access_log /var/log/nginx/farm-erp.access.log;
    error_log /var/log/nginx/farm-erp.error.log;
}
EOF

# Enable the site
ln -sf /etc/nginx/sites-available/farm-erp /etc/nginx/sites-enabled/

# Remove default nginx site
rm -f /etc/nginx/sites-enabled/default

# Test the configuration
echo "Testing nginx configuration..."
if nginx -t; then
    echo "✅ Nginx configuration test passed!"
    
    # Start nginx
    systemctl start nginx
    systemctl enable nginx
    
    # Check status
    if systemctl is-active --quiet nginx; then
        echo "✅ Nginx is running successfully!"
        echo "🌐 Your server should now be accessible at: http://128.199.60.244"
    else
        echo "❌ Nginx failed to start"
        systemctl status nginx
    fi
else
    echo "❌ Nginx configuration test failed:"
    nginx -t
fi

echo ""
echo "Nginx setup completed!"