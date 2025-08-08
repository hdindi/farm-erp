# SSH Key Setup for Digital Ocean

## Method 1: Using Windows (PowerShell)

Open PowerShell as Administrator and run:

```powershell
# Generate SSH key pair
ssh-keygen -t ed25519 -C "farm-erp-server-key"

# When prompted:
# - File location: Press Enter (use default)
# - Passphrase: Enter a secure passphrase (recommended)

# Display your public key (copy this entire output)
Get-Content ~/.ssh/id_ed25519.pub
```

## Method 2: Using Git Bash (if you have Git installed)

```bash
# Generate SSH key pair
ssh-keygen -t ed25519 -C "farm-erp-server-key"

# Display your public key (copy this entire output)
cat ~/.ssh/id_ed25519.pub
```

## Method 3: Using PuTTY (Alternative for Windows)

1. Download PuTTYgen from putty.org
2. Open PuTTYgen
3. Click "Generate" and move mouse randomly
4. Copy the public key from the text box
5. Save private key (with passphrase)

## Adding SSH Key to Digital Ocean

1. In Digital Ocean console, go to Settings → Security
2. Click "Add SSH Key"
3. Paste your public key in the text box
4. Name it: "Farm ERP Server Key"
5. Click "Add SSH Key"

## Important Notes

- **Never share your private key** (`id_ed25519` or `.ppk` file)
- **Only share the public key** (`id_ed25519.pub`)
- **Keep your private key secure** - this is your server access
- **Remember your passphrase** if you set one

Your public key should look like:
```
ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIGQw... farm-erp-server-key
```