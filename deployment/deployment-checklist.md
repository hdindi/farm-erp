# Farm ERP Deployment Verification Checklist

## Pre-Deployment Checklist
- [ ] Digital Ocean droplet created and accessible via SSH
- [ ] Managed MySQL database created and accessible
- [ ] Domain DNS configured to point to droplet IP
- [ ] Server dependencies installed (PHP, Nginx, Node.js, etc.)
- [ ] SSL certificate installed and working
- [ ] GitHub repository secrets configured

## Application Deployment Checklist
- [ ] Repository cloned to `/var/www/farm-erp`
- [ ] `.env` file configured with production settings
- [ ] Composer dependencies installed
- [ ] NPM dependencies installed and assets built
- [ ] Application key generated
- [ ] Database migrations run successfully
- [ ] Storage permissions set correctly
- [ ] Nginx virtual host configured
- [ ] PHP-FPM configured and running

## Post-Deployment Verification

### 1. Website Accessibility
- [ ] Site loads at https://your-domain.com
- [ ] SSL certificate is valid (green padlock)
- [ ] No mixed content warnings
- [ ] All static assets load correctly (CSS, JS, images)

### 2. Application Functionality
- [ ] Login page accessible
- [ ] User registration works
- [ ] Dashboard loads after login
- [ ] Database operations work (CRUD operations)
- [ ] File uploads work (if applicable)
- [ ] Email functionality works (password reset, notifications)

### 3. Performance Tests
```bash
# Test page load times
curl -w "Time: %{time_total}s\n" -o /dev/null -s https://your-domain.com

# Test database connectivity
cd /var/www/farm-erp && php artisan migrate:status

# Check PHP-FPM status
systemctl status php8.2-fpm

# Check Nginx status
systemctl status nginx

# Check disk space
df -h

# Check memory usage
free -m
```

### 4. Security Tests
- [ ] HTTP redirects to HTTPS
- [ ] Admin routes require authentication
- [ ] File upload restrictions work
- [ ] SQL injection protection (test with basic payloads)
- [ ] XSS protection (test with basic scripts)

### 5. Monitoring and Backups
- [ ] Health check script runs successfully
- [ ] Backup script creates complete backups
- [ ] Log files are being written
- [ ] Digital Ocean monitoring is active
- [ ] SSL certificate auto-renewal is configured

### 6. GitHub Actions Pipeline
- [ ] Tests pass in GitHub Actions
- [ ] Deployment workflow triggers on push to main
- [ ] Application deploys successfully via GitHub Actions
- [ ] Health checks pass after deployment

## Common Issues and Solutions

### Issue: 500 Internal Server Error
**Solutions:**
```bash
# Check Laravel logs
tail -f /var/www/farm-erp/storage/logs/laravel.log

# Check Nginx error logs
tail -f /var/log/nginx/farm-erp.error.log

# Check PHP-FPM logs
tail -f /var/log/php8.2-fpm.log

# Clear all caches
cd /var/www/farm-erp
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Issue: Database Connection Error
**Solutions:**
```bash
# Test database connection manually
mysql -h your-db-host -P 25060 -u doadmin -p

# Check .env database configuration
cat /var/www/farm-erp/.env | grep DB_

# Verify database exists
mysql -h your-db-host -P 25060 -u doadmin -p -e "SHOW DATABASES;"
```

### Issue: Permission Errors
**Solutions:**
```bash
# Set correct ownership
chown -R www-data:www-data /var/www/farm-erp

# Set correct permissions
chmod -R 755 /var/www/farm-erp/storage
chmod -R 755 /var/www/farm-erp/bootstrap/cache
```

### Issue: Assets Not Loading
**Solutions:**
```bash
# Rebuild assets
cd /var/www/farm-erp
npm run build

# Clear view cache
php artisan view:clear

# Check public directory permissions
ls -la /var/www/farm-erp/public
```

## Performance Optimization

### PHP-FPM Tuning
```bash
# Edit PHP-FPM pool configuration
nano /etc/php/8.2/fpm/pool.d/www.conf

# Recommended settings for 2GB RAM:
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
```

### Nginx Optimization
```bash
# Edit main Nginx configuration
nano /etc/nginx/nginx.conf

# Add these settings in http block:
client_max_body_size 10M;
keepalive_timeout 30;
client_body_timeout 10;
client_header_timeout 10;
```

### Laravel Optimization
```bash
cd /var/www/farm-erp

# Enable OPcache for PHP
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize Composer autoloader
composer install --optimize-autoloader --no-dev
```

## Maintenance Commands

### Daily Maintenance
```bash
# Clear logs older than 7 days
find /var/www/farm-erp/storage/logs -name "*.log" -mtime +7 -delete

# Clear expired sessions
cd /var/www/farm-erp && php artisan session:gc

# Update SSL certificate (if needed)
certbot renew --dry-run
```

### Weekly Maintenance
```bash
# Update system packages
apt update && apt upgrade -y

# Restart services
systemctl restart php8.2-fpm
systemctl restart nginx

# Run backup
/root/backup.sh
```

## Emergency Procedures

### Application Down
1. Check service status: `systemctl status nginx php8.2-fpm`
2. Check error logs: `tail -f /var/log/nginx/farm-erp.error.log`
3. Restart services: `systemctl restart nginx php8.2-fpm`
4. If database issues: Check database connectivity
5. Enable maintenance mode: `php artisan down`

### Data Recovery
1. Stop application: `php artisan down`
2. Restore from backup: Use latest backup from `/var/backups/farm-erp/`
3. Restore database: `mysql -h HOST -P PORT -u USER -p DB < backup.sql`
4. Clear caches: `php artisan cache:clear`
5. Restart application: `php artisan up`

## Success Criteria

✅ **Deployment is successful when:**
- Application loads without errors
- All major functionality works
- SSL is properly configured
- Monitoring is active
- Backups are running
- Performance is acceptable (page load < 3 seconds)
- Security headers are present
- Database operations work correctly