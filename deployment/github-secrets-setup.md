# GitHub Repository Secrets Configuration

To enable automated deployments via GitHub Actions, you need to add these secrets to your GitHub repository:

## Step 1: Go to Your GitHub Repository
1. Navigate to your farm-erp repository on GitHub
2. Go to Settings → Secrets and variables → Actions
3. Click "New repository secret" for each of the following:

## Step 2: Add These Secrets

### SSH_PRIVATE_KEY
- **Name**: `SSH_PRIVATE_KEY`
- **Value**: Your private SSH key content
- **To get this**: 
  ```bash
  # On Windows, in PowerShell:
  Get-Content ~/.ssh/id_ed25519
  
  # Or if using Git Bash:
  cat ~/.ssh/id_ed25519
  ```
  Copy the entire content including the BEGIN and END lines.

### HOST
- **Name**: `HOST`
- **Value**: `128.199.60.244`

### USERNAME
- **Name**: `USERNAME` 
- **Value**: `root`

### DOMAIN
- **Name**: `DOMAIN`
- **Value**: `128.199.60.244`

## Step 3: Update GitHub Actions Workflow

The workflow files are already configured to use these secrets. Once you add them, deployments will work automatically when you push to the main branch.

## Important Notes:
- Keep your SSH private key secure
- Never commit secrets to your repository
- Test the connection manually first before relying on GitHub Actions

## Testing SSH Connection:
Before setting up GitHub Actions, test your SSH connection:

```bash
ssh root@128.199.60.244
```

This should connect without asking for a password (using your SSH key).