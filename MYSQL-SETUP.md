# MySQL Setup Instructions for Farm ERP

## Overview
This application has been converted from SQLite to MySQL/MariaDB to match the Digital Ocean production environment.

## Configuration Changes Made

### 1. Database Configuration
- **`.env`**: Updated to use MySQL instead of SQLite
- **`config/database.php`**: Changed default connection to MySQL
- **`config/queue.php`**: Updated queue and batching connections to MySQL
- **`.env.example`**: Updated for MySQL defaults

### 2. Migration Compatibility
- All migrations are MySQL compatible
- CHECK constraints properly handled for MySQL vs SQLite
- Database views created with MySQL-compatible syntax

## MySQL Installation Options

### Option 1: XAMPP (Recommended for Windows)
1. Download XAMPP from https://www.apachefriends.org/
2. Install XAMPP with MySQL component
3. Start Apache and MySQL from XAMPP Control Panel
4. MySQL will be available at `localhost:3306`
5. Default credentials: username=`root`, password=`""` (empty)

### Option 2: MySQL Server Direct Installation
1. Download MySQL Server from https://dev.mysql.com/downloads/mysql/
2. Install MySQL Server
3. Set root password during installation
4. Update `.env` file with your password

### Option 3: WAMP Server
1. Download WAMP from https://www.wampserver.com/
2. Install WAMP with MySQL
3. Access MySQL through phpMyAdmin at http://localhost/phpmyadmin

### Option 4: Laravel Sail (Docker) - Requires WSL2
```bash
# Only works on WSL2, not Git Bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
```

## Database Setup Steps

### 1. Create Database
Once MySQL is running, create the database:

```sql
-- Option A: Using MySQL command line
mysql -u root -p
CREATE DATABASE farm_erp;
EXIT;

-- Option B: Using phpMyAdmin
-- Navigate to http://localhost/phpmyadmin
-- Click "New" to create database named "farm_erp"
```

### 2. Update Environment Configuration
Ensure your `.env` file has the correct settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=farm_erp
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

### 3. Run Migrations
```bash
# Clear any cached config
php artisan config:clear

# Test database connection
php artisan tinker
DB::connection()->getPdo();
exit

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### 4. Start Application
```bash
# Start Laravel development server
php artisan serve

# Start Vite for frontend assets (in new terminal)
npm run dev
```

## Testing the Conversion

### 1. Verify Database Connection
```bash
php artisan tinker
```
```php
// Test connection
DB::connection()->getPdo();

// Check tables exist
DB::select('SHOW TABLES');

// Test a query
App\Models\Batch::count();
```

### 2. Check Application Features
- Visit http://127.0.0.1:8000/batches
- Visit http://127.0.0.1:8000/daily-records
- Verify DataTables functionality works
- Test CRUD operations

## Migration from SQLite Data (if needed)

If you have existing SQLite data to migrate:

```bash
# Export SQLite data
sqlite3 database/database.sqlite .dump > sqlite_dump.sql

# Convert SQLite syntax to MySQL syntax (manual editing required)
# - Remove SQLite-specific syntax
# - Adjust data types if needed
# - Import to MySQL using phpMyAdmin or command line
```

## Troubleshooting

### Common Issues:

1. **Connection Refused**
   - Ensure MySQL service is running
   - Check port 3306 is not blocked
   - Verify credentials in `.env`

2. **Access Denied**
   - Check username/password in `.env`
   - Ensure MySQL user has proper permissions

3. **Database Not Found**
   - Create database manually: `CREATE DATABASE farm_erp;`

4. **Migration Errors**
   - Clear config cache: `php artisan config:clear`
   - Check MySQL version compatibility

### Configuration Commands:
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Check current database config
php artisan tinker
config('database.default')
config('database.connections.mysql')
```

## Production Deployment

For Digital Ocean MySQL deployment:
1. Update `.env` with production database credentials
2. Run migrations on production: `php artisan migrate --force`
3. Ensure MySQL version compatibility between local and production

## Next Steps

1. Install MySQL using one of the options above
2. Create the `farm_erp` database
3. Update `.env` with your MySQL credentials
4. Run `php artisan migrate`
5. Test the application functionality

The application is now fully configured for MySQL/MariaDB and ready for production deployment!