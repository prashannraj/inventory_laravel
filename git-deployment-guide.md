# Git-Based Deployment for Shared Hosting

## Overview
This guide explains how to set up a Git-based deployment workflow where you push code to GitHub and pull it via terminal in shared hosting.

## Current Git Configuration
- **Repository**: `https://github.com/prashannraj/inventory_laravel.git`
- **Branch**: `main`
- **Local Status**: 3 commits ahead of origin (need to push)

## Prerequisites for Git Deployment

### 1. Shared Hosting Requirements
- SSH access with Git installed
- Terminal access (cPanel Terminal, SSH, or similar)
- PHP 8.2+ and MySQL 5.7+
- Composer access (optional but recommended)

### 2. GitHub Configuration
- Repository exists: `prashannraj/inventory_laravel`
- SSH keys or HTTPS access from hosting
- Webhook setup (optional for auto-deployment)

## Deployment Workflow

```
Local Development → Git Commit → Git Push to GitHub → SSH to Hosting → Git Pull → Run Deployment Scripts
```

## Step-by-Step Setup

### Step 1: Initial GitHub Push
First, push your current code to GitHub:

```bash
# Add deployment files to git
git add deploy-instructions.md deploy-shared-hosting.sh QUICK-DEPLOYMENT-CHECKLIST.md
git add git-deployment-guide.md

# Commit changes
git commit -m "Add deployment documentation and scripts"

# Push to GitHub
git push origin main
```

### Step 2: Set Up Shared Hosting for Git

#### Option A: SSH Access Available
1. SSH into your hosting:
   ```bash
   ssh username@yourdomain.com
   ```

2. Navigate to your subdomain directory:
   ```bash
   cd /home/username/public_html/subdomain
   ```

3. Clone the repository:
   ```bash
   git clone https://github.com/prashannraj/inventory_laravel.git .
   # Note the dot (.) clones into current directory
   ```

#### Option B: cPanel Terminal
1. Open cPanel Terminal
2. Navigate to your subdomain directory
3. Clone the repository:
   ```bash
   git clone https://github.com/prashannraj/inventory_laravel.git .
   ```

#### Option C: Manual Git Setup
If Git isn't installed, request your hosting provider to install it or use these alternatives:

1. **Download ZIP from GitHub**:
   ```bash
   wget https://github.com/prashannraj/inventory_laravel/archive/refs/heads/main.zip
   unzip main.zip
   mv inventory_laravel-main/* .
   rm -rf inventory_laravel-main main.zip
   ```

2. **Use PHP to pull updates** (see automation scripts below)

### Step 3: Initial Deployment Setup on Hosting

After cloning, run the initial setup:

```bash
# Copy environment file
cp .env.example .env

# Set permissions
chmod -R 755 storage bootstrap/cache

# Install dependencies (if Composer available)
composer install --no-dev --optimize-autoloader

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --class=DatabaseSeeder
```

### Step 4: Create Deployment Scripts

Create a deployment script on your hosting:

```bash
#!/bin/bash
# deploy.sh - Git deployment script for shared hosting

echo "Starting deployment..."

# Pull latest changes
git pull origin main

# Install/update dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment completed!"
```

Save this as `deploy.sh` on your hosting and make it executable:
```bash
chmod +x deploy.sh
```

## Automated Deployment Options

### Option 1: Manual Deployment (Your Requested Workflow)
1. Make changes locally
2. Commit and push to GitHub:
   ```bash
   git add .
   git commit -m "Your commit message"
   git push origin main
   ```
3. SSH into hosting and run:
   ```bash
   cd /path/to/your/subdomain
   ./deploy.sh
   ```

### Option 2: Webhook Auto-Deployment
Set up GitHub webhooks to automatically trigger deployment when you push:

1. **Create a deployment endpoint** on your hosting:
   ```php
   <?php
   // deploy-webhook.php
   $secret = 'your_secret_key';
   $signature = $_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '';
   
   if ($signature) {
       $payload = file_get_contents('php://input');
       $hash = 'sha1=' . hash_hmac('sha1', $payload, $secret);
       
       if (hash_equals($hash, $signature)) {
           // Execute deployment
           shell_exec('cd /path/to/your/subdomain && ./deploy.sh 2>&1');
           echo "Deployment triggered";
       }
   }
   ?>
   ```

2. **Configure GitHub Webhook**:
   - Go to repository Settings → Webhooks → Add webhook
   - Payload URL: `https://your-subdomain.com/deploy-webhook.php`
   - Content type: `application/json`
   - Secret: your secret key
   - Events: "Just the push event"

### Option 3: Cron Job for Regular Pulls
Set up a cron job to periodically pull updates:

```bash
# Edit crontab
crontab -e

# Add line to check every 5 minutes
*/5 * * * * cd /home/username/public_html/subdomain && git pull origin main > /dev/null 2>&1
```

## Shared Hosting Specific Considerations

### 1. Git Installation on Shared Hosting
Most shared hosting providers have Git available via SSH. If not:

- Request Git installation from hosting support
- Use cPanel's "Git Version Control" feature
- Use alternative deployment methods (ZIP download)

### 2. File Permissions
After Git pull, you may need to reset permissions:

```bash
# Fix permissions after git operations
chmod -R 755 storage bootstrap/cache
chown -R username:username .  # If you have sudo access
```

### 3. Environment Configuration
Keep `.env` file outside Git but create a template:

```bash
# After first deployment, protect .env from being overwritten
git update-index --assume-unchanged .env
```

Or use a deployment script that preserves `.env`:

```bash
#!/bin/bash
# Backup .env before pull
cp .env .env.backup
git pull origin main
# Restore .env if it was overwritten
if [ -f .env.backup ]; then
    mv .env.backup .env
fi
```

### 4. Database Migrations
Automate migrations in your deployment script:

```bash
#!/bin/bash
# Safe migration script
php artisan migrate --force --no-interaction

# If migration fails, send notification
if [ $? -ne 0 ]; then
    echo "Migration failed!" >&2
    # Add notification logic here
fi
```

## Complete Deployment Scripts

### Script 1: Full Deployment Script (`deploy-full.sh`)
```bash
#!/bin/bash
# Full deployment script with error handling

set -e  # Exit on error

echo "🚀 Starting deployment at $(date)"

# Backup current state
echo "📦 Backing up current state..."
cp .env .env.backup 2>/dev/null || true

# Pull latest changes
echo "⬇️  Pulling latest changes from GitHub..."
git fetch origin
git reset --hard origin/main

# Restore environment file
echo "🔧 Restoring environment configuration..."
if [ -f .env.backup ]; then
    mv .env.backup .env
    echo "✅ .env restored from backup"
else
    echo "⚠️  No .env backup found, using existing .env"
fi

# Install dependencies
echo "📦 Installing dependencies..."
if command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader --no-interaction
else
    echo "⚠️  Composer not found, skipping dependency installation"
fi

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force --no-interaction

# Clear and cache
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
echo "🔐 Setting file permissions..."
chmod -R 755 storage bootstrap/cache

echo "✅ Deployment completed successfully at $(date)"
```

### Script 2: Simple Pull Script (`git-pull.sh`)
```bash
#!/bin/bash
# Simple git pull script for quick updates

cd /path/to/your/subdomain
git pull origin main
php artisan migrate --force
php artisan cache:clear
```

### Script 3: PHP-Based Deployment (No SSH Required)
Create `deploy.php` for web-based deployment:

```php
<?php
// deploy.php - Web-based deployment script
// Password protect this file or restrict by IP

$allowed_ips = ['your.ip.address.here'];
$password = 'your_deployment_password';

// Security check
if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips) && 
    (!isset($_GET['token']) || $_GET['token'] !== $password)) {
    die('Access denied');
}

echo "<pre>";
echo "Starting deployment...\n";

// Execute deployment commands
$commands = [
    'cd ' . __DIR__,
    'git pull origin main 2>&1',
    'composer install --no-dev --optimize-autoloader 2>&1',
    'php artisan migrate --force 2>&1',
    'php artisan cache:clear 2>&1',
];

foreach ($commands as $command) {
    echo "Executing: $command\n";
    echo shell_exec($command) . "\n";
    echo str_repeat('-', 50) . "\n";
}

echo "Deployment completed!";
echo "</pre>";
?>
```

## Troubleshooting Git Deployment

### Common Issues and Solutions

#### 1. "Git command not found"
- Request Git installation from hosting provider
- Use cPanel's Git interface
- Use alternative deployment method

#### 2. Permission denied after git pull
```bash
# Fix file ownership and permissions
chmod -R 755 storage bootstrap/cache
find storage -type f -exec chmod 644 {} \;
```

#### 3. Merge conflicts on .env
```bash
# Use ours strategy for .env
git checkout --ours .env
git add .env
```

#### 4. Composer not available
- Upload vendor directory manually
- Request Composer installation
- Use PHP-based dependency manager

#### 5. Database connection errors
- Verify `.env` file after pull
- Check database credentials
- Ensure migrations don't conflict

## Security Best Practices

### 1. Protect Deployment Scripts
```bash
# Restrict access to deployment scripts
chmod 700 deploy.sh
chmod 600 deploy.php
```

### 2. Use SSH Keys Instead of Passwords
```bash
# Generate SSH key on hosting
ssh-keygen -t rsa -b 4096

# Add to GitHub
cat ~/.ssh/id_rsa.pub
# Copy to GitHub Settings → SSH and GPG keys
```

### 3. Environment Security
- Never commit `.env` to Git
- Use different database credentials for production
- Rotate application keys periodically

### 4. Webhook Security
- Use secret tokens for webhooks
- Validate IP addresses
- Log deployment activities

## Monitoring and Logging

### 1. Deployment Logs
Add logging to your deployment script:

```bash
#!/bin/bash
LOG_FILE="storage/logs/deployments.log"

echo "[$(date)] Starting deployment" >> "$LOG_FILE"

# Your deployment commands...

echo "[$(date)] Deployment completed" >> "$LOG_FILE"
```

### 2. Error Notifications
Set up email notifications for failed deployments:

```bash
#!/bin/bash
if ! git pull origin main; then
    echo "Git pull failed" | mail -s "Deployment Failed" admin@example.com
    exit 1
fi
```

## Quick Start Checklist

### Initial Setup
- [ ] Push current code to GitHub: `git push origin main`
- [ ] SSH into shared hosting
- [ ] Clone repository: `git clone https://github.com/prashannraj/inventory_laravel.git .`
- [ ] Copy `.env.example` to `.env` and configure
- [ ] Run `chmod +x deploy.sh`
- [ ] Execute initial deployment: `./deploy.sh`

### Regular Deployment Workflow
1. [ ] Make changes locally
2. [ ] Commit: `git commit -m "Description"`
3. [ ] Push: `git push origin main`
4. [ ] SSH to hosting: `ssh username@host`
5. [ ] Run: `cd /path/to/app && ./deploy.sh`

### Optional Automation
- [ ] Set up GitHub webhook for auto-deployment
- [ ] Create cron job for scheduled pulls
- [ ] Configure deployment notifications

## Support and Resources

- **Git Documentation**: https://git-scm.com/doc
- **GitHub Help**: https://docs.github.com
- **Laravel Deployment**: https://laravel.com/docs/deployment
- **Shared Hosting Guides**: Check your hosting provider's documentation

## Files Created
- `git-deployment-guide.md` - This comprehensive guide
- Deployment scripts (to be created on hosting)
- Webhook scripts (optional)

With this setup, you can easily deploy updates by simply pushing to GitHub and running a single command on your shared hosting.