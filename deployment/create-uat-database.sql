-- SQL commands to create UAT database in Digital Ocean managed MySQL
-- Connect to your managed database using:
-- mysql -h dbaas-db-2779280-do-user-24512826-0.a.db.ondigitalocean.com -P 25060 -u doadmin -p

-- Create the UAT database
CREATE DATABASE IF NOT EXISTS farm_erp_uat;

-- Verify the database was created
SHOW DATABASES;

-- Optional: Create a dedicated UAT user (recommended for better security)
-- CREATE USER 'farm_erp_uat'@'%' IDENTIFIED BY 'secure_uat_password';
-- GRANT ALL PRIVILEGES ON farm_erp_uat.* TO 'farm_erp_uat'@'%';
-- FLUSH PRIVILEGES;