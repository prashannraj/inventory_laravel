# Shared Hosting Git Configuration Guide

## Overview
This guide explains how to configure your shared hosting environment for Git-based deployment.

## Prerequisites Check

### 1. Verify Git Availability
SSH into your hosting or use cPanel Terminal and run:
```bash
git --version
```

If Git is not installed, you'll need to:
- Request installation from hosting provider
- Use cPanel's "Git Version Control" feature
- Install Git manually (if you have sufficient permissions)

### 2. Check SSH Access
```bash
ssh username@yourdomain.com
```

If SSH is not enabled:
- Request SSH access from hosting provider
- Use cPanel Terminal instead
- Use FTP/SFTP for file transfers

### 3. Verify PHP and Composer
```bash
php --version
composer --version  # If available
```

## Configuration Methods

### Method 1: cPanel Git Version Control (Easiest)

#### Step 1: Access cPanel Git Interface
1. Login to cPanel
2. Find "Git Version Control" or "Git™ Manager"
3. Click "Create"

#### Step 2: Clone Repository
- Repository URL: `https://github.com/prashannraj/inventory_laravel.git`
- Repository Path: `/home/username/public_html/subdomain`
- Branch: `main`
- Click "Create"

#### Step 3: Initial Setup
After cloning:
```bash
cd /home/username/public_html/subdomain
cp .env.example .env
# Edit .env with your configuration
chmod -R 755 storage bootstrap/cache
```

#### Step 4: Pull Updates
Via cPanel interface:
1. Go to Git Version Control
2. Find your repository
3. Click "Manage"
4. Click "Pull or Deploy"

Or via terminal:
```bash
cd /home/username/public_html/subdomain
git pull origin main
```

### Method 2: Manual SSH Git Setup

#### Step 1: SSH Access
```bash
ssh username@yourdomain.com
cd /home/username/public_html/subdomain
```

#### Step 2: Clone Repository
```bash
# If directory is empty
git clone https://github.com/prashannraj/inventory_laravel.git .

# If directory has files, clone to temp and move
git clone https://github.com/prashannraj/inventory_laravel.git /tmp/inventory
cp -r /tmp/inventory/* .
cp -r /tmp/inventory/.* . 2>/dev/null || true
rm -rf /tmp/inventory
```

#### Step 3: Configure Git
```bash
# Set git configuration
git config user.email "you@example.com"
git config user.name "Your Name"

# Set up remote tracking
git branch --set-upstream-to=origin/main main
```

### Method 3: Using SSH Keys (More Secure)

#### Step 1: Generate SSH Key on Hosting
```bash
ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
# Press Enter for default location
# Enter passphrase (optional)
```

#### Step 2: Add Public Key to GitHub
```bash
cat ~/.ssh/id_rsa.pub
```
1. Copy the output
2. Go to GitHub → Settings → SSH and GPG Keys → New SSH Key
3. Paste the key and save

#### Step 3: Clone Using SSH
```bash
git clone git@github.com:prashannraj/inventory_laravel.git .
```

### Method 4: No Git Available (Alternative)

If Git is not available on your hosting:

#### Option A: Use cPanel File Manager
1. Download ZIP from GitHub: `https://github.com/prashannraj/inventory_laravel/archive/refs/heads/main.zip`
2. Upload to hosting via cPanel File Manager
3. Extract in your subdomain directory

#### Option B: Use PHP Deployment Script
Create `update.php`:
```php
<?php
// update.php - PHP-based updater
$zipUrl = 'https://github.com/prashannraj/inventory_laravel/archive/refs/heads/main.zip';
$backupDir = 'backup_' . date('Ymd_His');

// Backup current files
mkdir($backupDir);
system("cp -r . $backupDir/");

// Download and extract update
file_put_contents('update.zip', file_get_contents($zipUrl));
$zip = new ZipArchive;
if ($zip->open('update.zip') === TRUE) {
    $zip->extractTo('.');
    $zip->close();
    unlink('update.zip');
    
    // Move files from extracted directory
    system("mv inventory_laravel-main/* .");
    system("rm -rf inventory_laravel-main");
    
    echo "Update completed!";
} else {
    echo "Update failed!";
}
?>
```

## Directory Structure Setup

### Recommended Structure
```
/home/username/
├── public_html/
│   ├── subdomain/          # Your application
│   │   ├── .git/           # Git repository
│   │   ├── public/         # Web root
│   │   ├── storage/        # Laravel storage
│   │   └── .env            # Environment config
│   └── maindomain/         # Your main site
```

### Web Server Configuration

#### Apache (Most Shared Hosting)
Ensure your `.htaccess` in the root directory contains:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

#### Nginx (If Available)
You may need to request Nginx configuration from your hosting provider.

## Permission Configuration

### Initial Permissions
```bash
# Set directory permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 775 storage/logs

# Set file permissions
find storage -type f -exec chmod 664 {} \;
find bootstrap/cache -type f -exec chmod 664 {} \;
```

### Git-Specific Permissions
```bash
# Ensure git can write files
chmod -R 775 .git
chmod 644 .git/config

# Fix ownership issues (if you have sudo)
chown -R username:username .
```

## Environment Configuration

### .env File Protection
To prevent `.env` from being overwritten by Git:
```bash
# Tell Git to ignore changes to .env
git update-index --assume-unchanged .env

# To track changes again later
git update-index --no-assume-unchanged .env
```

### Environment Backup Script
Create `backup-env.sh`:
```bash
#!/bin/bash
# Backup .env before git operations
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
```

## Testing Git Access

### Test 1: Basic Git Operations
```bash
cd /home/username/public_html/subdomain
git status
git log --oneline -5
```

### Test 2: Pull Test
```bash
git fetch --dry-run
```

### Test 3: Push Test (If You Need to Push from Hosting)
```bash
# Make a test change
echo "# Test" >> README.md
git add README.md
git commit -m "Test commit"
git push origin main
```

## Troubleshooting

### Issue 1: "Git command not found"
**Solutions**:
1. Request Git installation from hosting support
2. Use cPanel's Git Version Control
3. Use alternative deployment methods

### Issue 2: Permission Denied
```bash
# Fix permissions
chmod -R 755 .
find . -type f -exec chmod 644 {} \;
chmod -R 775 storage bootstrap/cache
```

### Issue 3: SSH Key Authentication Failed
```bash
# Test SSH connection to GitHub
ssh -T git@github.com

# Regenerate keys if needed
ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa_new
```

### Issue 4: Merge Conflicts
```bash
# Backup current state
cp -r . ../backup_before_merge

# Use ours strategy for config files
git checkout --ours .env
git add .env
git commit -m "Resolve merge conflict in .env"
```

### Issue 5: Out of Memory During Composer Install
```bash
# Increase PHP memory limit
php -d memory_limit=512M /usr/local/bin/composer install

# Or install without dev dependencies
composer install --no-dev --optimize-autoloader
```

## Security Considerations

### 1. Protect .git Directory
Add to `.htaccess`:
```apache
# Deny access to .git directory
RedirectMatch 404 /\.git

# Or use Files directive
<FilesMatch "^\.git">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### 2. Secure Deployment Scripts
```bash
# Restrict access to deployment scripts
chmod 700 deploy.sh
chmod 600 deploy.php

# Move sensitive scripts outside web root if possible
mv deploy.sh ../deploy.sh
```

### 3. Use Different Database Credentials
- Production database should use different credentials than development
- Regularly rotate database passwords
- Limit database user privileges

## Automation Setup

### Cron Job for Automatic Pulls
```bash
# Edit crontab
crontab -e

# Add line to pull every hour
0 * * * * cd /home/username/public_html/subdomain && git pull origin main > /dev/null 2>&1

# Or run deployment script
0 * * * * cd /home/username/public_html/subdomain && ./deploy.sh > /dev/null 2>&1
```

### Webhook Configuration (Advanced)
See `git-deployment-guide.md` for webhook setup instructions.

## Maintenance

### Regular Tasks
1. **Check Git Status**: `git status`
2. **Review Logs**: `tail -f storage/logs/deployments.log`
3. **Monitor Disk Space**: `df -h`
4. **Backup Database**: Regular MySQL dumps

### Update Procedure
```bash
# Standard update procedure
cd /home/username/public_html/subdomain
./deploy.sh

# Or manual update
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan cache:clear
```

## Support Resources

### Hosting Provider Documentation
- Check your hosting provider's knowledge base for Git setup
- Contact support for SSH/Git installation requests

### GitHub Documentation
- [GitHub SSH Key Setup](https://docs.github.com/en/authentication/connecting-to-github-with-ssh)
- [GitHub CLI](https://cli.github.com/)

### Laravel Deployment
- [Laravel Deployment Documentation](https://laravel.com/docs/deployment)
- [Laravel Forge](https://forge.laravel.com/) (Alternative for managed hosting)

## Quick Reference

### Common Commands
```bash
# Pull updates
git pull origin main

# Check status
git status

# View history
git log --oneline -10

# Reset to remote state
git fetch origin
git reset --hard origin/main

# Fix permissions after git operations
chmod -R 755 storage bootstrap/cache
```

### File Locations
- **Git Config**: `~/.gitconfig` or `/home/username/.gitconfig`
- **SSH Keys**: `~/.ssh/id_rsa` and `~/.ssh/id_rsa.pub`
- **Application Logs**: `storage/logs/`
- **Deployment Logs**: `storage/logs/deployments.log`

With this configuration, your shared hosting environment will be ready for Git-based deployment workflow.