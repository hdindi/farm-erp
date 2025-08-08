#!/bin/bash

# SSL Setup for IP Address (Testing Only)
# Note: SSL certificates cannot be issued for IP addresses with Let's Encrypt
# This script will skip SSL setup for testing purposes

IP_ADDRESS="128.199.60.244"

echo "⚠️  SSL Certificate Setup for IP Address"
echo "========================================"
echo ""
echo "Note: Let's Encrypt cannot issue SSL certificates for IP addresses."
echo "Your application will be available at: http://$IP_ADDRESS"
echo ""
echo "For production with SSL, you'll need:"
echo "1. A domain name pointing to this IP"
echo "2. To run the original ssl-setup.sh script"
echo ""
echo "Current setup is HTTP-only for testing purposes."
echo "This is fine for testing, but not recommended for production."
echo ""
echo "✅ Nginx is configured to serve HTTP traffic on port 80"
echo "✅ Your application will be accessible at: http://$IP_ADDRESS"
echo ""
echo "To add SSL later:"
echo "1. Set up your domain (farm-erp.harrisdindi.com)"
echo "2. Update nginx configuration with domain name"
echo "3. Run: /root/ssl-setup.sh"