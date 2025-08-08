#!/bin/bash

# Farm ERP Backup Script
# Configure these variables with your actual details

APP_NAME="farm-erp"
APP_PATH="/var/www/farm-erp"
BACKUP_PATH="/var/backups/farm-erp"
DATE=$(date +%Y%m%d_%H%M%S)

# Digital Ocean Database Configuration
DB_HOST="dbaas-db-2779280-do-user-24512826-0.m.db.ondigitalocean.com"
DB_PORT="25060"
DB_NAME="farm-erp-db"
DB_USER="doadmin"
DB_PASS="YOUR_DATABASE_PASSWORD_HERE"

# Digital Ocean Spaces Configuration (Optional)
SPACES_KEY="your-spaces-key"
SPACES_SECRET="your-spaces-secret"
SPACES_ENDPOINT="https://nyc3.digitaloceanspaces.com"
SPACES_BUCKET="farm-erp-backups"

# Create backup directory
mkdir -p $BACKUP_PATH

echo "Starting backup process for $APP_NAME..."

# 1. Database Backup
echo "Backing up database..."
mysqldump -h $DB_HOST -P $DB_PORT -u $DB_USER -p$DB_PASS --single-transaction --routines --triggers $DB_NAME > $BACKUP_PATH/database_$DATE.sql

# 2. Application Files Backup (excluding vendor, node_modules, cache)
echo "Backing up application files..."
cd /var/www
tar -czf $BACKUP_PATH/application_$DATE.tar.gz \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='storage/logs' \
    --exclude='storage/framework/cache' \
    --exclude='storage/framework/sessions' \
    --exclude='storage/framework/views' \
    --exclude='.git' \
    farm-erp/

# 3. Storage Files Backup
echo "Backing up storage files..."
tar -czf $BACKUP_PATH/storage_$DATE.tar.gz -C $APP_PATH storage/app/

# 4. Environment Configuration Backup
echo "Backing up configuration..."
cp $APP_PATH/.env $BACKUP_PATH/env_$DATE.backup

# 5. Create combined backup
echo "Creating combined backup archive..."
cd $BACKUP_PATH
tar -czf farm-erp_full_backup_$DATE.tar.gz \
    database_$DATE.sql \
    application_$DATE.tar.gz \
    storage_$DATE.tar.gz \
    env_$DATE.backup

# 6. Upload to Digital Ocean Spaces (if configured)
if command -v s3cmd &> /dev/null && [ ! -z "$SPACES_KEY" ]; then
    echo "Uploading backup to Digital Ocean Spaces..."
    s3cmd put farm-erp_full_backup_$DATE.tar.gz s3://$SPACES_BUCKET/
fi

# 7. Clean up old backups (keep last 7 days)
echo "Cleaning up old backups..."
find $BACKUP_PATH -name "*.sql" -mtime +7 -delete
find $BACKUP_PATH -name "*.tar.gz" -mtime +7 -delete
find $BACKUP_PATH -name "*.backup" -mtime +7 -delete

echo "Backup completed successfully!"
echo "Backup location: $BACKUP_PATH/farm-erp_full_backup_$DATE.tar.gz"
echo "Database backup: $BACKUP_PATH/database_$DATE.sql"

# 8. Send backup notification (optional)
if command -v mail &> /dev/null; then
    echo "Backup completed for $APP_NAME on $(hostname) at $(date)" | mail -s "Farm ERP Backup Completed" your-email@domain.com
fi