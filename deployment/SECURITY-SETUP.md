# Security Setup Instructions

## ⚠️ IMPORTANT: Manual Configuration Required

After deployment, you must manually configure these sensitive values on your server:

### Database Configuration
```bash
# SSH into your server
ssh root@128.199.60.244

# Edit the .env file with real credentials
nano /var/www/farm-erp/.env

# Update these values:
DB_PASSWORD=YOUR_DATABASE_PASSWORD_HERE
```

### Backup Script Configuration
```bash
# Edit backup script with real credentials
nano /root/deployment/backup.sh

# Update this value:
DB_PASS="YOUR_DATABASE_PASSWORD_HERE"
```

## Security Best Practices
- ✅ Never commit passwords to Git
- ✅ Use environment variables for secrets
- ✅ Keep credentials in secure, encrypted storage
- ✅ Rotate passwords regularly
- ✅ Use strong, unique passwords

## Your Database Details (for reference)
- Host: dbaas-db-2779280-do-user-24512826-0.m.db.ondigitalocean.com
- Port: 25060
- Database: farm-erp-db
- Username: doadmin
- Password: [Configure manually on server]