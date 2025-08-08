# Upload Instructions for Farm ERP Deployment

## Step 1: Update Configuration Files

Before uploading, replace these placeholders in your files:

### In `production-config.sh`:
- `YOUR_DROPLET_IP_HERE` → Your actual droplet IP
- `YOUR_DOMAIN_HERE` → Your actual domain (e.g., farm-erp.example.com)
- `YOUR_DB_HOST_HERE` → Your database host from DO console
- `YOUR_DB_PASSWORD_HERE` → Your database password from DO console
- `your-email@domain.com` → Your actual email

### In `nginx-config`:
- `farm-erp.yourdomain.com` → Your actual domain

### In `ssl-setup.sh`:
- `farm-erp.yourdomain.com` → Your actual domain
- `your-email@domain.com` → Your actual email

## Step 2: Upload Files to Server

### Method 1: Using SCP (Recommended)
```bash
# From your project directory
scp -r deployment/ root@YOUR_DROPLET_IP:/root/

# Upload your application code
scp -r . root@YOUR_DROPLET_IP:/root/farm-erp-source/
```

### Method 2: Using Git (Alternative)
```bash
# SSH into your server first
ssh root@YOUR_DROPLET_IP

# Clone your repository
git clone https://github.com/yourusername/farm-erp.git /var/www/farm-erp

# Copy deployment files
cp /root/deployment/* /root/
```

### Method 3: Using FileZilla (GUI Option)
1. Download FileZilla Client
2. Connect with SFTP:
   - Host: YOUR_DROPLET_IP
   - Username: root
   - Port: 22
   - Use your SSH private key file
3. Upload deployment folder to /root/

## Step 3: Connect to Your Server

```bash
# Connect via SSH
ssh root@YOUR_DROPLET_IP

# If using SSH key with passphrase, you'll be prompted for it
```

## Troubleshooting Connection Issues

### If SSH connection fails:
1. Check your droplet is running in DO console
2. Verify you're using the correct IP address
3. Ensure your SSH key was added during droplet creation
4. Try connecting from a different network (some ISPs block certain ports)

### If you get permission denied:
1. Verify your SSH private key path: `~/.ssh/id_ed25519`
2. Check key permissions: `chmod 600 ~/.ssh/id_ed25519`
3. Use verbose mode to debug: `ssh -v root@YOUR_DROPLET_IP`

### Alternative connection methods:
1. Use Digital Ocean Console Access (in droplet overview)
2. Use password authentication (if enabled during creation)
3. Reset root password via DO console if needed