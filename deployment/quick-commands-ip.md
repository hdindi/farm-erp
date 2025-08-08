# Quick Commands for Harris's Farm ERP Setup (IP Address Testing)

## Your Server Details:
- **Server IP**: 128.199.60.244
- **Application URL**: http://128.199.60.244 (HTTP only for testing)
- **SSH Key**: C:\Users\aaa\OneDrive\Documents\Laptop Public Key

## Step 1: Upload Files
```powershell
# From your project directory in PowerShell
scp -r deployment\ root@128.199.60.244:/root/
```

## Step 2: Connect to Server
```bash
ssh root@128.199.60.244
```

## Step 3: Run Complete Setup (IP Version)
```bash
# This single command does everything for IP address testing
chmod +x /root/complete-setup-ip.sh && /root/complete-setup-ip.sh
```

## Step 4: Test Your Application
Visit: **http://128.199.60.244** (Note: HTTP, not HTTPS)

## Important Notes:
- ✅ **Works immediately** - No domain setup required
- ⚠️ **HTTP only** - SSL certificates can't be issued for IP addresses
- 🔄 **Easy to upgrade** - When you get a domain, we can easily add SSL

## Manual Configuration (if needed)
```bash
# If you need to manually edit .env after setup
nano /var/www/farm-erp/.env

# Copy your personalized .env file
cp /root/.env.production.harris /var/www/farm-erp/.env

# Generate application key
cd /var/www/farm-erp && php artisan key:generate

# Run migrations
php artisan migrate

# Set permissions
chown -R www-data:www-data /var/www/farm-erp
chmod -R 755 /var/www/farm-erp/storage
chmod -R 755 /var/www/farm-erp/bootstrap/cache

# Restart services
systemctl restart nginx php8.2-fpm
```

## Troubleshooting Commands:
```bash
# Check services status
systemctl status nginx php8.2-fpm

# Check logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log
tail -f /var/www/farm-erp/storage/logs/laravel.log

# Test database connection
cd /var/www/farm-erp && php artisan migrate:status

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## When You're Ready for a Domain:
1. Set up DNS: farm-erp.harrisdindi.com → 128.199.60.244
2. Update nginx config with domain name
3. Run SSL setup: `/root/ssl-setup.sh`
4. Update .env with HTTPS URL

## Your Database Connection Details:
- Host: dbaas-db-2779280-do-user-24512826-0.m.db.ondigitalocean.com
- Port: 25060
- Database: farm-erp-db
- Username: doadmin
- Password: YOUR_DATABASE_PASSWORD_HERE