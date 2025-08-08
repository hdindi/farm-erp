#!/bin/bash

# SSL Certificate Setup with Let's Encrypt
# Replace 'farm-erp.yourdomain.com' with your actual domain

DOMAIN="farm-erp.harrisdindi.com"
EMAIL="dindiharrissamuel@gmail.com"

echo "Setting up SSL certificates for $DOMAIN"

# Install Certbot
apt install -y snapd
snap install core; snap refresh core
snap install --classic certbot

# Create symlink for certbot command
ln -s /snap/bin/certbot /usr/bin/certbot

# Generate SSL certificate
certbot --nginx -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos --email $EMAIL --redirect

# Setup automatic renewal
systemctl enable snap.certbot.renew.timer
systemctl start snap.certbot.renew.timer

# Test renewal process
certbot renew --dry-run

echo "SSL certificate setup completed!"
echo "Your site should now be accessible via HTTPS"
echo "Automatic renewal is configured and will run twice daily"