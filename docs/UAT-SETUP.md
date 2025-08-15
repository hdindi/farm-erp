# UAT Environment Setup Guide

This guide explains how to set up the User Acceptance Testing (UAT) environment for the Farm ERP system.

## Overview

The UAT environment provides a production-like testing environment where:
- Features are tested before production deployment
- Stakeholders can review and approve changes
- Automated testing occurs on every push to the `uat` branch

## Environment Details

- **URL**: https://uat.kinjabi.farm
- **Branch**: `uat`
- **Database**: `farm_erp_uat`
- **Auto-deployment**: Enabled via GitHub Actions

## Prerequisites

1. **Server Access**: SSH access to your Digital Ocean server
2. **Domain**: DNS access to configure `uat.kinjabi.farm`
3. **GitHub Repository**: Repository with appropriate permissions
4. **SSL Certificate**: Let's Encrypt for HTTPS

## Quick Setup (Automated)

### 1. Run the Setup Script

```bash
# On your server
wget https://raw.githubusercontent.com/YOUR_USERNAME/farm-erp/uat/deployment/uat-setup.sh
chmod +x uat-setup.sh
sudo ./uat-setup.sh
```

### 2. Configure DNS

Add DNS record at Namecheap:
- **Type**: A Record
- **Host**: uat
- **Value**: 128.199.60.244
- **TTL**: Automatic

### 3. Set GitHub Secrets

In your GitHub repository → Settings → Secrets and variables → Actions:

```
UAT_HOST=128.199.60.244
UAT_USERNAME=root
UAT_SSH_KEY=[Your SSH private key]
UAT_PORT=22
```

## Manual Setup

### 1. Server Setup

```bash
# Create UAT directory
sudo mkdir -p /var/www/farm-erp-uat
cd /var/www/farm-erp-uat

# Clone repository
sudo git clone https://github.com/YOUR_USERNAME/farm-erp.git .
sudo git checkout uat

# Install dependencies
sudo composer install --optimize-autoloader --no-dev

# Create Laravel directories
sudo mkdir -p storage/{app/public,framework/{cache/data,sessions,views},logs}
sudo mkdir -p bootstrap/cache

# Set permissions
sudo chown -R www-data:www-data /var/www/farm-erp-uat
sudo chmod -R 755 /var/www/farm-erp-uat
sudo chmod -R 775 /var/www/farm-erp-uat/storage
sudo chmod -R 775 /var/www/farm-erp-uat/bootstrap/cache
```

### 2. Environment Configuration

```bash
# Copy UAT environment file
sudo cp .env.uat .env

# Generate application key
sudo -u www-data php artisan key:generate

# Create storage link
sudo -u www-data php artisan storage:link
```

### 3. Database Setup

```bash
# Create UAT database
mysql -u root -p
```

```sql
CREATE DATABASE farm_erp_uat;
GRANT ALL PRIVILEGES ON farm_erp_uat.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
# Update .env with database password
sudo nano .env
# Set DB_PASSWORD=your_password

# Run migrations
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan db:seed --force
```

### 4. Nginx Configuration

```bash
# Copy Nginx configuration
sudo cp deployment/nginx-uat.conf /etc/nginx/sites-available/uat-kinjabi-farm

# Enable site
sudo ln -s /etc/nginx/sites-available/uat-kinjabi-farm /etc/nginx/sites-enabled/

# Test and reload
sudo nginx -t
sudo systemctl reload nginx
```

### 5. SSL Setup

```bash
# Install SSL certificate
sudo certbot --nginx -d uat.kinjabi.farm --email your-email@example.com --agree-tos --no-eff-email
```

## Local Development with Docker

For local UAT testing, use Docker Compose:

```bash
# Start UAT environment locally
docker-compose -f docker-compose.uat.yml up -d

# Access locally
http://localhost:8081

# View emails (MailHog)
http://localhost:8026
```

## GitHub Actions Workflow

The UAT deployment is automated via GitHub Actions:

### Trigger Events
- Push to `uat` branch
- Pull request to `uat` branch

### Workflow Steps
1. **Test Suite**: Runs PHPUnit tests
2. **Deploy**: If tests pass, deploys to UAT server
3. **Backup**: Creates backup before deployment
4. **Update**: Pulls latest code and dependencies
5. **Database**: Runs migrations
6. **Optimize**: Caches configurations
7. **Restart**: Restarts services

### Monitoring Deployment

1. **GitHub Actions**: Check workflow status in GitHub
2. **Server Logs**: Monitor deployment logs
3. **Application**: Test UAT site functionality

```bash
# Check deployment logs
tail -f /var/log/nginx/uat.kinjabi.farm_error.log

# Check Laravel logs
tail -f /var/www/farm-erp-uat/storage/logs/laravel.log
```

## Deployment Workflow

### Development Process

1. **Feature Development**
   ```bash
   git checkout dev
   git pull origin dev
   # Make changes
   git add .
   git commit -m "Feature: Add new functionality"
   git push origin dev
   ```

2. **UAT Deployment**
   ```bash
   git checkout uat
   git merge dev
   git push origin uat  # Triggers auto-deployment
   ```

3. **Production Deployment**
   ```bash
   git checkout main
   git merge uat
   git push origin main  # Manual production deployment
   ```

### Branch Strategy

- **`dev`**: Development branch for ongoing work
- **`uat`**: User acceptance testing branch
- **`main`**: Production-ready code

## Testing Checklist

Before promoting to production, verify:

- [ ] Application loads correctly
- [ ] User authentication works
- [ ] Database operations function
- [ ] File uploads work
- [ ] Email notifications send
- [ ] API endpoints respond
- [ ] Mobile responsiveness
- [ ] Performance is acceptable

## Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   sudo chown -R www-data:www-data /var/www/farm-erp-uat
   sudo chmod -R 775 /var/www/farm-erp-uat/storage
   ```

2. **Database Connection Issues**
   ```bash
   # Check database credentials in .env
   php artisan config:clear
   ```

3. **SSL Certificate Issues**
   ```bash
   sudo certbot renew --dry-run
   ```

4. **GitHub Actions Failing**
   - Check secrets configuration
   - Verify SSH key access
   - Review server logs

### Log Files

- **Nginx**: `/var/log/nginx/uat.kinjabi.farm_error.log`
- **Laravel**: `/var/www/farm-erp-uat/storage/logs/laravel.log`
- **PHP**: `/var/log/php8.2-fpm.log`

## Security Considerations

- UAT environment includes `noindex, nofollow` headers
- Database is isolated from production
- Environment variables are properly secured
- SSL encryption is enforced
- Sensitive files are blocked via Nginx

## Maintenance

### Regular Tasks

1. **Database Backups**
   ```bash
   mysqldump -u root -p farm_erp_uat > uat_backup_$(date +%Y%m%d).sql
   ```

2. **Log Rotation**
   ```bash
   sudo logrotate -f /etc/logrotate.d/nginx
   ```

3. **SSL Renewal**
   ```bash
   sudo certbot renew
   ```

4. **Dependency Updates**
   ```bash
   composer update --no-dev
   ```

## Support

For issues with the UAT environment:

1. Check the troubleshooting section above
2. Review GitHub Actions logs
3. Monitor server logs
4. Contact the development team

---

**Last Updated**: $(date)
**Environment Version**: UAT v1.0