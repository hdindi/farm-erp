# Quick Commands for Harris's Farm ERP Setup

## Your Server Details:
- **Server IP**: 128.199.60.244
- **Domain**: farm-erp.harrisdindi.com
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

## Step 3: Run Complete Setup
```bash
# This single command does everything
chmod +x /root/complete-setup.sh && /root/complete-setup.sh
```

## Step 4: Manual Configuration (if needed)
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

## Step 5: Test Your Application
Visit: https://farm-erp.harrisdindi.com

## Troubleshooting Commands:
```bash
# Check services status
systemctl status nginx php8.2-fpm

# Check logs
tail -f /var/log/nginx/farm-erp.error.log
tail -f /var/www/farm-erp/storage/logs/laravel.log

# Test database connection
cd /var/www/farm-erp && php artisan migrate:status

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Your Database Connection Details:
- Host: dbaas-db-2779280-do-user-24512826-0.m.db.ondigitalocean.com
- Port: 25060
- Database: farm-erp-db
- Username: doadmin
- Password: YOUR_DATABASE_PASSWORD_HERE