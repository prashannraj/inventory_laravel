#!/bin/bash
# Git Deployment Script for Laravel Inventory Application
# To be used on shared hosting after cloning repository

set -e  # Exit on error

echo "========================================="
echo "🚀 Laravel Inventory - Git Deployment"
echo "========================================="
echo "Started at: $(date)"
echo ""

# Configuration
REPO_URL="https://github.com/prashannraj/inventory_laravel.git"
BRANCH="main"
APP_DIR=$(pwd)
LOG_FILE="$APP_DIR/storage/logs/deployments.log"

# Create log directory if it doesn't exist
mkdir -p "$(dirname "$LOG_FILE")"

# Log function
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log "Starting deployment process..."

# Check if we're in a git repository
if [ ! -d ".git" ]; then
    log "❌ Not a git repository. Initializing..."
    
    # Check if directory is empty
    if [ "$(ls -A $APP_DIR 2>/dev/null | head -1)" ]; then
        log "⚠️  Directory not empty. Backing up existing files..."
        BACKUP_DIR="backup_$(date +%Y%m%d_%H%M%S)"
        mkdir -p "../$BACKUP_DIR"
        cp -r . "../$BACKUP_DIR/" 2>/dev/null || true
        log "Backup created at: ../$BACKUP_DIR"
    fi
    
    # Initialize git and clone
    git init
    git remote add origin "$REPO_URL"
    git fetch origin
    git checkout -t origin/"$BRANCH" || git checkout -b "$BRANCH"
    log "✅ Git repository initialized"
else
    log "✅ Git repository found"
fi

# Backup critical files
log "📦 Backing up critical files..."
if [ -f ".env" ]; then
    cp .env .env.backup.deploy
    log "  - .env backed up"
fi

if [ -f "storage/oauth-private.key" ]; then
    cp storage/oauth-private.key storage/oauth-private.key.backup 2>/dev/null || true
fi

if [ -f "storage/oauth-public.key" ]; then
    cp storage/oauth-public.key storage/oauth-public.key.backup 2>/dev/null || true
fi

# Pull latest changes
log "⬇️  Pulling latest changes from $BRANCH branch..."
git fetch origin
git reset --hard origin/"$BRANCH"
log "✅ Code updated to latest version"

# Restore backed up files
log "🔧 Restoring configuration..."
if [ -f ".env.backup.deploy" ]; then
    mv .env.backup.deploy .env
    log "  - .env restored"
else
    if [ ! -f ".env" ] && [ -f ".env.example" ]; then
        log "⚠️  No .env found, copying from .env.example"
        cp .env.example .env
        log "  - Please edit .env with your configuration"
    fi
fi

# Set proper permissions
log "🔐 Setting file permissions..."
chmod -R 755 storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage/logs 2>/dev/null || true

# Create necessary directories
log "📁 Creating required directories..."
mkdir -p storage/framework/cache 2>/dev/null || true
mkdir -p storage/framework/sessions 2>/dev/null || true
mkdir -p storage/framework/views 2>/dev/null || true
mkdir -p storage/logs 2>/dev/null || true
mkdir -p bootstrap/cache 2>/dev/null || true

# Install/update dependencies
log "📦 Checking dependencies..."
if command -v composer >/dev/null 2>&1; then
    log "  - Composer found, installing dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tee -a "$LOG_FILE"
    
    # Check if composer install succeeded
    if [ ${PIPESTATUS[0]} -eq 0 ]; then
        log "✅ Dependencies installed"
    else
        log "⚠️  Composer install had issues, trying update..."
        composer update --no-dev --optimize-autoloader --no-interaction 2>&1 | tee -a "$LOG_FILE"
    fi
else
    log "⚠️  Composer not found. Skipping dependency installation."
    log "  - Make sure vendor directory exists or install composer"
fi

# Generate application key if not set
log "🔑 Checking application key..."
if [ -f ".env" ]; then
    if grep -q "APP_KEY=" .env && ! grep -q "APP_KEY=base64:" .env; then
        log "  - Generating application key..."
        php artisan key:generate --force 2>&1 | tee -a "$LOG_FILE"
    else
        log "  - Application key already set"
    fi
fi

# Run database migrations
log "🗄️  Running database migrations..."
php artisan migrate --force --no-interaction 2>&1 | tee -a "$LOG_FILE"
if [ ${PIPESTATUS[0]} -eq 0 ]; then
    log "✅ Migrations completed"
else
    log "⚠️  Migrations may have failed. Check logs above."
fi

# Clear caches
log "🧹 Clearing caches..."
php artisan cache:clear 2>&1 | tee -a "$LOG_FILE"
php artisan config:clear 2>&1 | tee -a "$LOG_FILE"
php artisan view:clear 2>&1 | tee -a "$LOG_FILE"
log "✅ Caches cleared"

# Optimize for production
log "⚡ Optimizing for production..."
php artisan config:cache 2>&1 | tee -a "$LOG_FILE"
php artisan route:cache 2>&1 | tee -a "$LOG_FILE"
php artisan view:cache 2>&1 | tee -a "$LOG_FILE"
log "✅ Application optimized"

# Run additional optimizations
log "🔧 Running additional optimizations..."
php artisan storage:link 2>&1 | tee -a "$LOG_FILE" || true
php artisan optimize 2>&1 | tee -a "$LOG_FILE" || true

# Check if npm/build is needed
if [ -f "package.json" ] && command -v npm >/dev/null 2>&1; then
    log "📦 Installing npm dependencies..."
    npm install --production 2>&1 | tee -a "$LOG_FILE"
    
    if [ -f "vite.config.js" ] || [ -f "webpack.mix.js" ]; then
        log "🏗️  Building assets..."
        npm run build 2>&1 | tee -a "$LOG_FILE"
    fi
fi

# Final permissions check
log "🔒 Final permissions check..."
find storage -type f -exec chmod 664 {} \; 2>/dev/null || true
find bootstrap/cache -type f -exec chmod 664 {} \; 2>/dev/null || true
find storage -type d -exec chmod 775 {} \; 2>/dev/null || true
find bootstrap/cache -type d -exec chmod 775 {} \; 2>/dev/null || true

# Verify deployment
log "🔍 Verifying deployment..."
if [ -f "public/index.php" ]; then
    log "✅ Public index file exists"
else
    log "⚠️  Warning: public/index.php not found"
fi

if php artisan --version >/dev/null 2>&1; then
    ARTISAN_VERSION=$(php artisan --version 2>/dev/null | head -1)
    log "✅ Artisan working: $ARTISAN_VERSION"
else
    log "⚠️  Warning: Artisan may not be working properly"
fi

# Cleanup
log "🧽 Cleaning up..."
rm -f .env.backup.deploy 2>/dev/null || true
rm -f storage/oauth-private.key.backup 2>/dev/null || true
rm -f storage/oauth-public.key.backup 2>/dev/null || true

# Summary
log "========================================="
log "✅ Deployment completed successfully!"
log "========================================="
echo ""
echo "📊 Deployment Summary:"
echo "  - Code updated from GitHub"
echo "  - Dependencies installed"
echo "  - Database migrated"
echo "  - Caches cleared and optimized"
echo "  - Permissions set"
echo ""
echo "📝 Next steps:"
echo "  1. Visit your application URL to verify"
echo "  2. Check storage/logs/deployments.log for details"
echo "  3. Test key functionality"
echo ""
echo "🕒 Deployment completed at: $(date)"
echo "========================================="

# Log completion
log "Deployment process completed at $(date)"
log "========================================="