# GitHub Secrets Setup for UAT Deployment

This guide explains how to configure GitHub secrets for automated UAT deployment.

## Required Secrets

Navigate to your GitHub repository → **Settings** → **Secrets and variables** → **Actions**

### 1. UAT_HOST
- **Name**: `UAT_HOST`
- **Value**: `128.199.60.244`
- **Description**: IP address of your Digital Ocean server

### 2. UAT_USERNAME
- **Name**: `UAT_USERNAME`
- **Value**: `root`
- **Description**: SSH username for server access

### 3. UAT_SSH_KEY
- **Name**: `UAT_SSH_KEY`
- **Value**: Your SSH private key content
- **Description**: SSH private key for passwordless authentication

### 4. UAT_PORT (Optional)
- **Name**: `UAT_PORT`
- **Value**: `22`
- **Description**: SSH port (default is 22)

## SSH Key Setup

### 1. Generate SSH Key Pair (if you don't have one)

On your local machine:
```bash
ssh-keygen -t ed25519 -C "github-actions-uat" -f ~/.ssh/github_actions_uat
```

### 2. Add Public Key to Server

```bash
# Copy public key to server
ssh-copy-id -i ~/.ssh/github_actions_uat.pub root@128.199.60.244

# Or manually add to authorized_keys
cat ~/.ssh/github_actions_uat.pub | ssh root@128.199.60.244 "mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys"
```

### 3. Add Private Key to GitHub Secrets

```bash
# Display private key content
cat ~/.ssh/github_actions_uat
```

Copy the entire output (including `-----BEGIN OPENSSH PRIVATE KEY-----` and `-----END OPENSSH PRIVATE KEY-----`) and paste it as the `UAT_SSH_KEY` secret value.

### 4. Test SSH Connection

```bash
ssh -i ~/.ssh/github_actions_uat root@128.199.60.244
```

## Setting Up Secrets

### Via GitHub Web Interface

1. Go to your repository on GitHub
2. Click **Settings** (repository settings, not your account)
3. In the left sidebar, click **Secrets and variables**
4. Click **Actions**
5. Click **New repository secret**
6. Add each secret with the name and value specified above

### Via GitHub CLI (Alternative)

```bash
# Install GitHub CLI if not already installed
# https://cli.github.com/

# Authenticate
gh auth login

# Set secrets
gh secret set UAT_HOST --body "128.199.60.244"
gh secret set UAT_USERNAME --body "root"
gh secret set UAT_PORT --body "22"

# Set SSH key (from file)
gh secret set UAT_SSH_KEY < ~/.ssh/github_actions_uat
```

## Verification

### 1. Check Secrets Are Set

In your repository:
- Go to **Settings** → **Secrets and variables** → **Actions**
- Verify all required secrets are listed
- Note: Secret values are hidden for security

### 2. Test Deployment

Push a change to the `uat` branch to trigger the workflow:

```bash
git checkout uat
echo "# Test deployment" >> README.md
git add README.md
git commit -m "Test: Trigger UAT deployment"
git push origin uat
```

### 3. Monitor Workflow

1. Go to **Actions** tab in your repository
2. Click on the running workflow
3. Monitor the deployment process
4. Check for any errors in the logs

## Security Best Practices

### SSH Key Security
- Use dedicated SSH keys for GitHub Actions
- Regularly rotate SSH keys
- Use Ed25519 keys for better security
- Never commit private keys to the repository

### Secret Management
- Use least-privilege principle for SSH access
- Regularly audit repository secrets
- Remove unused secrets
- Use environment-specific secrets

### Access Control
- Limit repository access to authorized users
- Use branch protection rules
- Require reviews for sensitive changes
- Enable audit logging

## Troubleshooting

### Common Issues

1. **SSH Connection Failed**
   ```
   Error: Permission denied (publickey)
   ```
   - Verify SSH key is correctly added to server
   - Check key format in GitHub secrets
   - Ensure no extra whitespace in secret value

2. **Host Key Verification Failed**
   ```
   Error: Host key verification failed
   ```
   - Add server to known hosts in workflow (automatic)
   - Or disable strict host key checking (less secure)

3. **Port Connection Issues**
   ```
   Error: Connection refused
   ```
   - Verify UAT_HOST and UAT_PORT values
   - Check server firewall settings
   - Ensure SSH service is running

### Debug Steps

1. **Test SSH Locally**
   ```bash
   ssh -i ~/.ssh/github_actions_uat root@128.199.60.244 "echo 'Connection successful'"
   ```

2. **Check Server Logs**
   ```bash
   sudo tail -f /var/log/auth.log
   ```

3. **Verify GitHub Actions Logs**
   - Check the deployment workflow logs
   - Look for SSH connection errors
   - Verify script execution output

### Manual Verification

Test the exact commands used by GitHub Actions:

```bash
# Connect to server
ssh -i ~/.ssh/github_actions_uat root@128.199.60.244

# Run deployment commands manually
cd /var/www/farm-erp-uat
git fetch origin
git checkout uat
git pull origin uat
composer install --optimize-autoloader --no-dev
php artisan migrate --force
# ... etc
```

## Alternative Authentication Methods

### Using Deploy Keys (Repository-specific)

1. **Generate Deploy Key**
   ```bash
   ssh-keygen -t ed25519 -C "deploy-key-uat" -f ~/.ssh/deploy_key_uat
   ```

2. **Add to Repository Settings**
   - Go to **Settings** → **Deploy keys**
   - Add public key with write access

3. **Update Workflow**
   - Use deploy key in GitHub Actions
   - More secure for specific repositories

### Using SSH Agent

For more complex setups, consider using SSH agent forwarding or multiple keys.

## Maintenance

### Regular Tasks

- **Rotate SSH keys** every 6-12 months
- **Audit secret usage** quarterly
- **Update access permissions** as team changes
- **Monitor deployment logs** for security issues

### Key Rotation Process

1. Generate new SSH key pair
2. Add new public key to server
3. Update GitHub secret with new private key
4. Test deployment
5. Remove old public key from server
6. Delete old private key files

---

**Security Note**: Never share or commit SSH private keys. Always use GitHub secrets for sensitive information.