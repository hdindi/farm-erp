#!/bin/bash

# Monitoring and Alerting Setup for Farm ERP

echo "Setting up monitoring for Farm ERP..."

# Install monitoring tools
apt update
apt install -y htop iotop nethogs ncdu

# Create monitoring scripts directory
mkdir -p /opt/farm-erp-monitoring

# 1. System Health Check Script
cat > /opt/farm-erp-monitoring/health-check.sh << 'EOF'
#!/bin/bash

DOMAIN="128.199.60.244"
APP_PATH="/var/www/farm-erp"
LOG_FILE="/var/log/farm-erp-health.log"

echo "$(date): Starting health check" >> $LOG_FILE

# Check if application is responding
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://$DOMAIN)
if [ $HTTP_STATUS -eq 200 ]; then
    echo "$(date): ✅ Application responding (HTTP $HTTP_STATUS)" >> $LOG_FILE
else
    echo "$(date): ❌ Application not responding (HTTP $HTTP_STATUS)" >> $LOG_FILE
    # Restart services if needed
    systemctl restart php8.2-fpm
    systemctl restart nginx
fi

# Check disk usage
DISK_USAGE=$(df / | awk 'NR==2{print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 80 ]; then
    echo "$(date): ⚠️  High disk usage: ${DISK_USAGE}%" >> $LOG_FILE
fi

# Check memory usage
MEMORY_USAGE=$(free | awk 'NR==2{printf "%.0f", $3*100/$2}')
if [ $MEMORY_USAGE -gt 90 ]; then
    echo "$(date): ⚠️  High memory usage: ${MEMORY_USAGE}%" >> $LOG_FILE
fi

# Check Laravel logs for errors
if [ -f "$APP_PATH/storage/logs/laravel.log" ]; then
    ERROR_COUNT=$(grep -c "ERROR\|CRITICAL\|EMERGENCY" $APP_PATH/storage/logs/laravel.log | tail -100)
    if [ $ERROR_COUNT -gt 10 ]; then
        echo "$(date): ⚠️  High error count in Laravel logs: $ERROR_COUNT" >> $LOG_FILE
    fi
fi

# Check database connectivity
cd $APP_PATH
if php artisan migrate:status > /dev/null 2>&1; then
    echo "$(date): ✅ Database connection successful" >> $LOG_FILE
else
    echo "$(date): ❌ Database connection failed" >> $LOG_FILE
fi

echo "$(date): Health check completed" >> $LOG_FILE
EOF

chmod +x /opt/farm-erp-monitoring/health-check.sh

# 2. Log Rotation Setup
cat > /etc/logrotate.d/farm-erp << 'EOF'
/var/www/farm-erp/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
    postrotate
        systemctl reload php8.2-fpm
    endscript
}

/var/log/farm-erp-health.log {
    weekly
    missingok
    rotate 4
    compress
    delaycompress
    notifempty
}
EOF

# 3. Cron Jobs Setup
cat > /tmp/farm-erp-cron << 'EOF'
# Farm ERP Monitoring and Maintenance
*/5 * * * * /opt/farm-erp-monitoring/health-check.sh
0 2 * * * /root/backup.sh
0 3 * * 0 cd /var/www/farm-erp && php artisan cache:clear
EOF

crontab /tmp/farm-erp-cron

# 4. System Resource Monitoring
cat > /opt/farm-erp-monitoring/system-stats.sh << 'EOF'
#!/bin/bash

LOG_FILE="/var/log/farm-erp-system.log"
echo "$(date): System Statistics" >> $LOG_FILE
echo "CPU Usage: $(top -bn1 | grep "Cpu(s)" | sed "s/.*, *\([0-9.]*\)%* id.*/\1/" | awk '{print 100 - $1"%"}')" >> $LOG_FILE
echo "Memory Usage: $(free -m | awk 'NR==2{printf "%.1f%%", $3*100/$2 }')" >> $LOG_FILE
echo "Disk Usage: $(df -h / | awk 'NR==2{print $5}')" >> $LOG_FILE
echo "Load Average: $(uptime | awk -F'load average:' '{print $2}')" >> $LOG_FILE
echo "---" >> $LOG_FILE
EOF

chmod +x /opt/farm-erp-monitoring/system-stats.sh

# Add system stats to cron (every hour)
echo "0 * * * * /opt/farm-erp-monitoring/system-stats.sh" >> /tmp/farm-erp-cron
crontab /tmp/farm-erp-cron

echo "Monitoring setup completed!"
echo "Health checks will run every 5 minutes"
echo "Backups will run daily at 2 AM"
echo "System stats are logged hourly"
echo ""
echo "Log files:"
echo "- Health: /var/log/farm-erp-health.log"
echo "- System: /var/log/farm-erp-system.log"
echo "- Application: /var/www/farm-erp/storage/logs/laravel.log"