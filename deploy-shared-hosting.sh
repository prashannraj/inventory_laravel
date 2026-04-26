#!/bin/bash
# Deployment script for Laravel Inventory Application to Shared Hosting
# This script prepares the application for deployment to shared hosting

echo "========================================="
echo "Laravel Inventory App - Deployment Prep"
echo "========================================="

# Create deployment directory
DEPLOY_DIR="inventory-app-deploy-$(date +%Y%m%d_%H%M%S)"
echo "Creating deployment directory: $DEPLOY_DIR"
mkdir -p "$DEPLOY_DIR"

# Copy essential files
echo "Copying application files..."

# 1. Copy all files except those in .gitignore and development files
rsync -av --progress \
  --exclude='.git' \
  --exclude='.gitignore' \
  --exclude='.editorconfig' \
  --exclude='.env' \
  --exclude='.env.example' \
  --exclude='.env.backup' \
  --exclude='.env.production' \
  --exclude='.phpactor.json' \
  --exclude='.phpunit.result.cache' \
  --exclude='/.fleet' \
  --exclude='/.idea' \
  --exclude='/.nova' \
  --exclude='/.phpunit.cache' \
  --exclude='/.vscode' \
  --exclude='/.zed' \
  --exclude='/auth.json' \
  --exclude='/node_modules' \
  --exclude='/public/build' \
  --exclude='/public/hot' \
  --exclude='/public/storage' \
  --exclude='/storage/*.key' \
  --exclude='/storage/pail' \
  --exclude='/vendor' \
  --exclude='Homestead.json' \
  --exclude='Homestead.yaml' \
  --exclude='Thumbs.db' \
  --exclude='*.log' \
  --exclude='.DS_Store' \
  --exclude='deploy-shared-hosting.sh' \
  --exclude='deploy-instructions.md' \
  --exclude='composer.lock' \
  --exclude='package-lock.json' \
  --exclude='phpunit.xml' \
  --exclude='tests/' \
  . "$DEPLOY_DIR/"

# Create necessary directories
echo "Creating required directories..."
mkdir -p "$DEPLOY_DIR/storage/framework/cache"
mkdir -p "$DEPLOY_DIR/storage/framework/sessions"
mkdir -p "$DEPLOY_DIR/storage/framework/views"
mkdir -p "$DEPLOY_DIR/storage/logs"
mkdir -p "$DEPLOY_DIR/bootstrap/cache"

# Set permissions (for Unix-based systems)
echo "Setting directory permissions..."
chmod -R 755 "$DEPLOY_DIR/storage"
chmod -R 755 "$DEPLOY_DIR/bootstrap/cache"

# Create .env file template
echo "Creating .env template..."
cat > "$DEPLOY_DIR/.env.example" << 'EOF'
APP_NAME="Inventory Management"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://your-subdomain.your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
EOF

# Create deployment instructions file
echo "Creating deployment instructions..."
cat > "$DEPLOY_DIR/DEPLOYMENT-README.md" << 'EOF'
# Laravel Inventory Application - Deployment Instructions

## Prerequisites
1. Shared hosting with PHP 8.2+ support
2. MySQL database (5.7+ or MariaDB 10.3+)
3. SSH/FTP access to your hosting account
4. Subdomain or directory ready for deployment

## Deployment Steps

### 1. Upload Files
Upload ALL files from this directory to your hosting:
- Via FTP: Upload to your subdomain directory (e.g., public_html/subdomain)
- Via cPanel File Manager: Upload and extract zip file

### 2. Configure Database
1. Create a MySQL database via cPanel
2. Create a database user with full privileges
3. Note down: database name, username, password, and host (usually localhost)

### 3. Configure Environment
1. Rename `.env.example` to `.env`
2. Edit `.env` file with your configuration:
   - Update `APP_URL` to your actual subdomain URL
   - Update database credentials (DB_* settings)
   - Set `APP_DEBUG=false` for production

### 4. Generate Application Key
Via SSH (if available):
```bash
php artisan key:generate
```

Via cPanel Terminal (if available) or run this PHP script:
Create a file `generate_key.php` in your web root with:
```php
<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('key:generate');
echo "Application key generated!";
```
Then visit this file in browser, then delete it.

### 5. Set Directory Permissions
Set the following permissions via cPanel File Manager or FTP:
- `storage/` → 755 (recursive)
- `bootstrap/cache/` → 755 (recursive)

### 6. Run Database Migrations
Via SSH:
```bash
php artisan migrate --force
```

Or create a migration script `run_migrations.php`:
```php
<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('migrate', ['--force' => true]);
echo "Migrations completed!";
```

### 7. Install Dependencies
If your hosting has Composer installed:
```bash
composer install --no-dev --optimize-autoloader
```

If not, you need to upload the vendor directory from your local development.

### 8. Configure Web Server
Ensure your web server points to the `public/` directory.

For Apache (typical shared hosting), create or modify `.htaccess` in the root:
```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### 9. Test the Application
Visit your subdomain URL to verify the application loads.

### 10. Create Admin User
Via SSH:
```bash
php artisan db:seed --class=DatabaseSeeder
```

Or run the seeder via a PHP script.

## Troubleshooting

### White Screen/Blank Page
1. Check `storage/logs/laravel.log` for errors
2. Verify `.env` file exists and has correct permissions
3. Check PHP version (requires 8.2+)

### Database Connection Error
1. Verify database credentials in `.env`
2. Check if database host allows connections
3. Ensure database user has proper privileges

### 500 Internal Server Error
1. Check file permissions
2. Verify `storage/` and `bootstrap/cache/` are writable
3. Check PHP error logs in cPanel

## Security Notes
1. Delete any `.env.example` after creating `.env`
2. Never commit `.env` to version control
3. Keep `APP_DEBUG=false` in production
4. Regularly update dependencies

## Support
For issues, check Laravel documentation or contact your hosting provider.
EOF

# Create a simple PHP script for key generation and migrations
echo "Creating helper PHP scripts..."

cat > "$DEPLOY_DIR/generate_key.php" << 'EOF'
<?php
// Script to generate application key
echo "<h3>Laravel Application Key Generator</h3>";

if (!file_exists('.env')) {
    echo "Error: .env file not found. Please rename .env.example to .env first.";
    exit;
}

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    $status = $kernel->call('key:generate');
    echo "Application key generated successfully!<br>";
    echo "Please delete this file immediately after use.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
EOF

cat > "$DEPLOY_DIR/run_migrations.php" << 'EOF'
<?php
// Script to run database migrations
echo "<h3>Laravel Database Migrations</h3>";

if (!file_exists('.env')) {
    echo "Error: .env file not found. Please configure .env file first.";
    exit;
}

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    $status = $kernel->call('migrate', ['--force' => true]);
    echo "Database migrations completed successfully!<br>";
    echo "Please delete this file immediately after use.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Check your database configuration in .env file.";
}
EOF

# Create zip file for easy upload
echo "Creating zip archive..."
zip -r "$DEPLOY_DIR.zip" "$DEPLOY_DIR"

echo "========================================="
echo "Deployment preparation complete!"
echo "========================================="
echo ""
echo "Next steps:"
echo "1. Upload the '$DEPLOY_DIR' folder or '$DEPLOY_DIR.zip' to your hosting"
echo "2. Follow instructions in '$DEPLOY_DIR/DEPLOYMENT-README.md'"
echo "3. Or read the detailed deployment guide in 'deploy-instructions.md'"
echo ""
echo "For detailed instructions, see: deploy-instructions.md"